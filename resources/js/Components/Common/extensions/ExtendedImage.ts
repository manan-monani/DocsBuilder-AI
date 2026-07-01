import ImageResize from 'tiptap-extension-resize-image';

/**
 * Extended Image extension that preserves inline styles (float, margins, etc.)
 */
export const ExtendedImage = ImageResize.extend({
    addAttributes() {
        return {
            ...this.parent?.(),
            style: {
                default: null,
                parseHTML: (element) => element.getAttribute('style'),
                renderHTML: (attributes) => {
                    if (!attributes.style) {
                        return {};
                    }
                    return { style: attributes.style };
                },
            },
            'data-caption': {
                default: null,
                parseHTML: (element) => element.getAttribute('data-caption'),
                renderHTML: (attributes) => {
                    if (!attributes['data-caption']) {
                        return {};
                    }
                    return { 'data-caption': attributes['data-caption'] };
                },
            },
        };
    },
});

export default ExtendedImage;
