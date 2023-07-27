(function (blocks, element, blockEditor) {
    var el = element.createElement;
    var InnerBlocks = blockEditor.InnerBlocks;
    var allowedBlocks = ['es/edusharing-block'];

    blocks.registerBlockType('themenportal/portal-edusharing-grid', {
        title: 'EduSharing Grid',
        icon: '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"\n	 viewBox="0 0 19.2 13.2" style="enable-background:new 0 0 19.2 13.2;" xml:space="preserve">\n<style type="text/css">\n	.st0{fill:#A2A2A2;}\n</style>\n<path class="st0" d="M1.1,6.2h5v-6h-5V6.2z M1.1,13.2h5v-6h-5V13.2z M7.1,13.2h5v-6h-5V13.2z M13.1,13.2h5v-6h-5V13.2z M7.1,6.2h5\n	v-6h-5V6.2z M13.1,0.2v6h5v-6H13.1z"/>\n</svg>',
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