/**
 * ACT Test Prep - Markdown Rendering
 */

const MarkdownRenderer = {
    initialized: false,

    /**
     * Initialize marked.js
     */
    init() {
        if (this.initialized) return;
        
        if (typeof marked === 'undefined') {
            console.warn('Marked.js not loaded');
            return;
        }

        // Configure marked
        marked.setOptions({
            breaks: true,
            gfm: true,
            headerIds: true,
            mangle: false,
            sanitize: false,
            smartLists: true,
            smartypants: false,
            xhtml: false,
            highlight: (code, lang) => {
                if (typeof hljs !== 'undefined' && lang && hljs.getLanguage(lang)) {
                    try {
                        return hljs.highlight(code, { language: lang }).value;
                    } catch (e) {
                        console.error('Highlight error:', e);
                    }
                }
                return code;
            }
        });

        // Custom renderer
        const renderer = new marked.Renderer();

        // Add IDs to headers
        renderer.heading = (text, level) => {
            const slug = Utils.slugify(text);
            return `<h${level} id="${slug}" class="heading-anchor">${text}</h${level}>`;
        };

        // Open links in new tab for external URLs
        renderer.link = (href, title, text) => {
            const isExternal = href.startsWith('http') && !href.includes(window.location.host);
            const target = isExternal ? ' target="_blank" rel="noopener noreferrer"' : '';
            const titleAttr = title ? ` title="${title}"` : '';
            return `<a href="${href}"${titleAttr}${target}>${text}</a>`;
        };

        // Custom code block rendering
        renderer.code = (code, language) => {
            const validLang = language && typeof hljs !== 'undefined' && hljs.getLanguage(language);
            const highlighted = validLang 
                ? hljs.highlight(code, { language }).value 
                : Utils.escapeHtml(code);
            const langClass = language ? ` language-${language}` : '';
            return `<pre class="code-block${langClass}"><code>${highlighted}</code></pre>`;
        };

        // Table styling
        renderer.table = (header, body) => {
            return `<div class="table-container"><table class="table">${header}${body}</table></div>`;
        };

        // Blockquote styling
        renderer.blockquote = (quote) => {
            return `<blockquote class="blockquote">${quote}</blockquote>`;
        };

        // Image with lazy loading
        renderer.image = (href, title, text) => {
            const titleAttr = title ? ` title="${title}"` : '';
            return `<img src="${href}" alt="${text}"${titleAttr} loading="lazy" class="markdown-image">`;
        };

        marked.use({ renderer });
        this.initialized = true;
    },

    /**
     * Render markdown to HTML
     */
    render(content) {
        if (!content) return '';
        
        this.init();

        if (typeof marked === 'undefined') {
            // Fallback: basic rendering
            return this.basicRender(content);
        }

        try {
            // Pre-process for math protection
            const { processed, placeholders } = this.protectMath(content);
            
            // Render markdown
            let html = marked.parse(processed);
            
            // Restore math
            html = this.restoreMath(html, placeholders);
            
            return html;
        } catch (error) {
            console.error('Markdown render error:', error);
            return this.basicRender(content);
        }
    },

    /**
     * Basic markdown rendering fallback
     */
    basicRender(content) {
        let html = Utils.escapeHtml(content);
        
        // Headers
        html = html.replace(/^### (.+)$/gm, '<h3>$1</h3>');
        html = html.replace(/^## (.+)$/gm, '<h2>$1</h2>');
        html = html.replace(/^# (.+)$/gm, '<h1>$1</h1>');
        
        // Bold and italic
        html = html.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
        html = html.replace(/\*(.+?)\*/g, '<em>$1</em>');
        
        // Code
        html = html.replace(/`(.+?)`/g, '<code>$1</code>');
        
        // Links
        html = html.replace(/\[(.+?)\]\((.+?)\)/g, '<a href="$2">$1</a>');
        
        // Line breaks
        html = html.replace(/\n\n/g, '</p><p>');
        html = '<p>' + html + '</p>';
        
        return html;
    },

    /**
     * Protect math from markdown processing
     */
    protectMath(content) {
        const placeholders = [];
        let counter = 0;

        // Protect display math $$...$$
        content = content.replace(/\$\$([\s\S]+?)\$\$/g, (match) => {
            const placeholder = `%%MATH_DISPLAY_${counter}%%`;
            placeholders.push({ placeholder, content: match });
            counter++;
            return placeholder;
        });

        // Protect display math \[...\]
        content = content.replace(/\\\[([\s\S]+?)\\\]/g, (match) => {
            const placeholder = `%%MATH_DISPLAY_${counter}%%`;
            placeholders.push({ placeholder, content: match });
            counter++;
            return placeholder;
        });

        // Protect inline math $...$
        content = content.replace(/\$([^$\n]+?)\$/g, (match) => {
            const placeholder = `%%MATH_INLINE_${counter}%%`;
            placeholders.push({ placeholder, content: match });
            counter++;
            return placeholder;
        });

        // Protect inline math \(...\)
        content = content.replace(/\\\((.+?)\\\)/g, (match) => {
            const placeholder = `%%MATH_INLINE_${counter}%%`;
            placeholders.push({ placeholder, content: match });
            counter++;
            return placeholder;
        });

        return { processed: content, placeholders };
    },

    /**
     * Restore math after markdown processing
     */
    restoreMath(html, placeholders) {
        for (const { placeholder, content } of placeholders) {
            html = html.replace(placeholder, content);
        }
        return html;
    },

    /**
     * Render markdown in element
     */
    renderElement(element) {
        if (!element) return;
        
        const markdown = element.getAttribute('data-markdown') || element.textContent;
        element.innerHTML = this.render(markdown);
        
        // Trigger MathJax rendering
        if (typeof MathRenderer !== 'undefined') {
            MathRenderer.renderElement(element);
        }
    },

    /**
     * Render all markdown elements on page
     */
    renderAll(selector = '[data-markdown]') {
        const elements = document.querySelectorAll(selector);
        elements.forEach(el => this.renderElement(el));
    },

    /**
     * Generate table of contents from content
     */
    generateTOC(content) {
        const toc = [];
        const headingRegex = /^(#{1,6})\s+(.+)$/gm;
        let match;

        while ((match = headingRegex.exec(content)) !== null) {
            const level = match[1].length;
            const text = match[2];
            const slug = Utils.slugify(text);
            
            toc.push({ level, text, slug });
        }

        return toc;
    },

    /**
     * Render table of contents HTML
     */
    renderTOC(content) {
        const toc = this.generateTOC(content);
        
        if (toc.length === 0) return '';

        let html = '<nav class="table-of-contents"><h4>Table of Contents</h4><ul>';
        
        for (const item of toc) {
            const indent = 'pl-' + ((item.level - 1) * 4);
            html += `<li class="${indent}"><a href="#${item.slug}">${Utils.escapeHtml(item.text)}</a></li>`;
        }
        
        html += '</ul></nav>';
        return html;
    },

    /**
     * Extract excerpt from markdown
     */
    excerpt(content, length = 200) {
        if (!content) return '';
        
        // Remove headers
        let text = content.replace(/^#{1,6}\s+.+$/gm, '');
        
        // Remove markdown formatting
        text = text.replace(/[*_~`#]/g, '');
        
        // Remove links but keep text
        text = text.replace(/\[([^\]]+)\]\([^)]+\)/g, '$1');
        
        // Remove math
        text = text.replace(/\$\$[\s\S]+?\$\$/g, '');
        text = text.replace(/\$[^$]+\$/g, '');
        
        // Trim and truncate
        text = text.trim();
        return Utils.truncate(text, length);
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    MarkdownRenderer.init();
});

// Make available globally
window.MarkdownRenderer = MarkdownRenderer;
