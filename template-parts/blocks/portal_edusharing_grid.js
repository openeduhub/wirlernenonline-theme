(function (blocks, element, blockEditor) {
    var el = element.createElement;
    var InnerBlocks = blockEditor.InnerBlocks;
    var allowedBlocks = ['es/edusharing-block'];

    blocks.registerBlockType('themenportal/portal-edusharing-grid', {
        title: 'EduSharing Grid',
        icon: 'media-document',
        category: 'themenportal',
        edit: function (props) {
            return el(
                'div',
                {
                    className: props.className
                },
                el('div', {className: 'backend_border'}, el('div', {className: 'backend_hint'},'Themenportal: Edu-Sharing Grid'),el(InnerBlocks, {
                    allowedBlocks: allowedBlocks
                }))
            )
        },

        save: function (props) {
            return el(
                'div',
                {
                    className: props.className
                },
                el(InnerBlocks.Content)
            );
        }
    });
}(
    window.wp.blocks,
    window.wp.element,
    window.wp.blockEditor,
));