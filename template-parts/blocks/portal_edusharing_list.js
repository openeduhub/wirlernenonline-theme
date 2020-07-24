( function( blocks, element, blockEditor ) {
    var el = element.createElement;
    var InnerBlocks = blockEditor.InnerBlocks;
    var allowedBlocks = [ 'es/edusharing-block' ];
    var customClassName = "portal_block";

    blocks.registerBlockType( 'themenportal/portal-edusharing-list', {
        title: 'EduSharing Liste',
        icon: 'media-document',
        category: 'themenportal',
        edit: function( props ) {
            return el(
                'div',
                {
                    className: props.className
                },
                el('div', {className: 'backend_border'}, el('div', {className: 'backend_hint'},'Themenportal: Edu-Sharing Liste'),el(InnerBlocks, {
                    allowedBlocks: allowedBlocks
                }))
            )
        },

        save: function( props ) {
            return el(
                'div',
                {
                    className: props.className
                },
                el( InnerBlocks.Content )
            );
        }
    } );
} (
    window.wp.blocks,
    window.wp.element,
    window.wp.blockEditor,
) );