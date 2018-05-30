(function(ns) {
    let Asset = ns.Asset = Hilo.Class.create({
        mixes: Hilo.EventMixin,
        queue: null,
        bg: null,
        ground: null,
        birdAtlas: null,
        ready: null,
        over: null,
        holdback: null,
        numberGraphs: null,
        load: function () {
            let resources = [
                {id: 'bg', src: '/img/flappy-bird/bg.png'},
                {id: 'bird', src: '/img/flappy-bird/bird.png'},
                {id: 'ground', src: '/img/flappy-bird/ground.png'},
                {id: 'holdback', src: '/img/flappy-bird/holdback.png'},
                {id: 'icon', src: '/img/flappy-bird/icon.png'},
                {id: 'number', src: '/img/flappy-bird/number.png'},
                {id: 'over', src: '/img/flappy-bird/over.png'},
                {id: 'ready', src: '/img/flappy-bird/ready.png'},
            ];
            // init Hilo 资源加载队列
            this.queue = new Hilo.LoadQueue();
            this.queue.add(resources);
            // 绑定加载完成事件
            this.queue.on('complete', this.complete.bind(this));
            this.queue.start();
        },
        complete: function (e) {
            this.bg = this.queue.get('bg').content;
            this.ground = this.queue.get('ground').content;
            this.ready = this.queue.get('ready').content;
            this.over = this.queue.get('over').content;
            this.holdback = this.queue.get('holdback').content;
            // 创建纹理图片集合
            this.birdAtlas = new Hilo.TextureAtlas({
                // 图集资源
                image: this.queue.get('bird').content,
                // 帧定义
                frames: [
                    [0, 120, 86, 60],
                    [0, 60, 86, 60],
                    [0, 0, 86, 60]
                ],
                // 精灵图顺序
                sprites: {
                    bird: [0, 1, 2]
                }
            });

            // 得分
            number = this.queue.get('number').content;
            this.numberGraphs = {
                0: {image:number, rect:[0,0,60,91]},
                1: {image:number, rect:[61,0,60,91]},
                2: {image:number, rect:[121,0,60,91]},
                3: {image:number, rect:[191,0,60,91]},
                4: {image:number, rect:[261,0,60,91]},
                5: {image:number, rect:[331,0,60,91]},
                6: {image:number, rect:[401,0,60,91]},
                7: {image:number, rect:[471,0,60,91]},
                8: {image:number, rect:[541,0,60,91]},
                9: {image:number, rect:[611,0,60,91]}
            };
            // 删除下载事件的complete监听 （？？？）
            this.queue.off('complete');
            this.fire('complete');
        }
    });
})(window.game);