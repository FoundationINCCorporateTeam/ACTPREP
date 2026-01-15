/**
 * ACT Test Prep - MathJax Integration
 */

const MathRenderer = {
    initialized: false,
    renderQueue: [],

    /**
     * Initialize MathJax
     */
    init() {
        if (this.initialized) return;

        // Wait for MathJax to load
        if (typeof MathJax === 'undefined') {
            setTimeout(() => this.init(), 100);
            return;
        }

        this.initialized = true;
        
        // Process any queued renders
        this.processQueue();
    },

    /**
     * Process queued renders
     */
    processQueue() {
        while (this.renderQueue.length > 0) {
            const element = this.renderQueue.shift();
            this.renderElement(element);
        }
    },

    /**
     * Render math in element
     */
    async renderElement(element) {
        if (!element) return;

        if (!this.initialized || typeof MathJax === 'undefined') {
            this.renderQueue.push(element);
            this.init();
            return;
        }

        try {
            // Clear previous MathJax output
            MathJax.typesetClear([element]);
            
            // Render new content
            await MathJax.typesetPromise([element]);
        } catch (error) {
            console.error('MathJax render error:', error);
        }
    },

    /**
     * Render math in multiple elements
     */
    async renderAll(selector = '.math-content') {
        const elements = document.querySelectorAll(selector);
        for (const element of elements) {
            await this.renderElement(element);
        }
    },

    /**
     * Re-render entire page
     */
    async renderPage() {
        if (typeof MathJax !== 'undefined') {
            try {
                await MathJax.typesetPromise();
            } catch (error) {
                console.error('MathJax page render error:', error);
            }
        }
    },

    /**
     * Convert LaTeX to SVG
     */
    async toSVG(latex, display = false) {
        if (typeof MathJax === 'undefined') {
            return latex;
        }

        try {
            const node = await MathJax.tex2svgPromise(latex, { display });
            return node.innerHTML;
        } catch (error) {
            console.error('MathJax toSVG error:', error);
            return latex;
        }
    },

    /**
     * Convert LaTeX to HTML
     */
    async toHTML(latex, display = false) {
        if (typeof MathJax === 'undefined') {
            return latex;
        }

        try {
            const node = await MathJax.tex2chtmlPromise(latex, { display });
            return node.innerHTML;
        } catch (error) {
            console.error('MathJax toHTML error:', error);
            return latex;
        }
    },

    /**
     * Prepare content for MathJax
     * Ensures proper delimiters
     */
    prepareContent(content) {
        if (!content) return content;

        // Convert common patterns to proper delimiters
        // $...$ for inline (already supported)
        // $$...$$ for display (already supported)
        // \(...\) for inline (already supported)
        // \[...\] for display (already supported)

        // Convert [math]...[/math] to $$...$$
        content = content.replace(/\[math\]([\s\S]*?)\[\/math\]/gi, '$$$$1$$');

        // Convert \begin{equation}...\end{equation} (already works, but ensure display)
        // No conversion needed, MathJax handles these

        return content;
    },

    /**
     * Check if content has math
     */
    hasMath(content) {
        if (!content) return false;
        
        // Check for common math delimiters
        const patterns = [
            /\$\$.+?\$\$/s,           // Display math $$...$$
            /\$[^$]+\$/,              // Inline math $...$
            /\\\(.+?\\\)/s,           // Inline \(...\)
            /\\\[.+?\\\]/s,           // Display \[...\]
            /\\begin\{equation\}/,    // LaTeX equation environment
            /\\frac\{/,               // Fraction
            /\\sqrt\{/,               // Square root
            /\^{/,                    // Superscript
            /_{/                      // Subscript
        ];

        return patterns.some(pattern => pattern.test(content));
    },

    /**
     * Escape special characters for math
     */
    escapeMath(text) {
        return text
            .replace(/\\/g, '\\\\')
            .replace(/\$/g, '\\$')
            .replace(/{/g, '\\{')
            .replace(/}/g, '\\}')
            .replace(/_/g, '\\_')
            .replace(/\^/g, '\\^');
    },

    /**
     * Create inline math element
     */
    inline(latex) {
        return `\\(${latex}\\)`;
    },

    /**
     * Create display math element
     */
    display(latex) {
        return `\\[${latex}\\]`;
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    MathRenderer.init();
});

// Also initialize when MathJax loads
window.addEventListener('load', () => {
    MathRenderer.init();
});

// Make available globally
window.MathRenderer = MathRenderer;
