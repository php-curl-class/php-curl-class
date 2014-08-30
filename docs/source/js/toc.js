function toc() {
    toc = $( '#toc' ).tocify({
        selectors: 'h1, h2',
        theme: 'none',
        smoothScroll: false,
        showEffectSpeed: 0,
        hideEffectSpeed: 180,
        hashGenerator: function (text, element) {
            return element.prop( 'id' );
        }
    }).data( 'toc-tocify' );
}
$(toc);
