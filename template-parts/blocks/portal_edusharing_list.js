( function( blocks, element, blockEditor ) {
    var el = element.createElement;
    var InnerBlocks = blockEditor.InnerBlocks;
    var allowedBlocks = [ 'es/edusharing-block' ];
    var customClassName = "portal_block";

    blocks.registerBlockType( 'themenportal/portal-edusharing-list', {
        title: 'EduSharing Liste',
        icon: '<svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path fill="none" d="M0 0h24v24H0z"></path><path style="fill: #a2a2a2" d="M3 15h18v-2H3v2zm0 4h18v-2H3v2zm0-8h18V9H3v2zm0-6v2h18V5H3z"></path></svg>',
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