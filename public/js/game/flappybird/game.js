(function(){
    window.onload = function() {
        game.init();
    }
    let game = window.game = {
        width: 0,
        height: 0,
        scale: 1,

        asset: null,
        // Asset: null,
        init: function() {
            this.asset = new game.Asset();
            // bind 'complete' event
            this.asset.on('complete', function(e) {
                this.asset.off('complete');// 释放
                this.initStage();
            }).bind(this);
            // start loading resources
            this.asset.load();
        },
        initStage: function() {
            this.width = Math.min(innerWidth, 450) * 2;
            this.height = Math.min(innerHeight, 750) * 2;
            this.scale = 0.5;


        }
    }
})();