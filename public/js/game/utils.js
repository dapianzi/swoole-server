if (!window.requestAnimationFrame) {
    window.requestAnimationFrame = (window.webkitRequestAnimationFrame ||
        window.mozRequestAnimationFrame ||
        window.oRequestAnimationFrame ||
        window.msRequestAnimationFrame ||
        function(callback) {
            return window.setTimeout(callback, 1000 / 60);
        });
}
class Pos {
    constructor(x, y) {
        this.x = x;
        this.y = y;
    }
    translate(x, y) {
        let mtx = new Matrix(3,3,[1,0,x,0,1,y,0,0,1]);
        let pos = new Matrix(3,1,[this.x, this.y, 1]);
        mtx.mul(pos);
        this.x = mtx.mtx[0][0];
        this.y = mtx.mtx[1][0];
        return this;
    }
    rotate(deg) {
        let rad = deg*Math.PI/180;
        let sinx = Math.sin(rad);
        let cosx = Math.cos(rad);
        let mtx = new Matrix(3,3,[cosx,sinx,0,-sinx,cosx,0,0,0,1]);
        let pos = new Matrix(3,1,[this.x, this.y, 1]);
        mtx.mul(pos);
        this.x = mtx.mtx[0][0];
        this.y = mtx.mtx[1][0];
        return this;
    }
    scale(sx, sy) {
        sy = sy || sx;
        let mtx = new Matrix(3,3,[sx,0,0,0,sy,0,0,0,1]);
        let pos = new Matrix(3,1,[this.x, this.y, 1]);
        mtx.mul(pos);
        this.x = mtx.mtx[0][0];
        this.y = mtx.mtx[1][0];
        return this;
    }
}
class Matrix {
    constructor(row, col, listArray) {
        this.row = row;
        this.col = col;
        this.mtx = [];
        let offset = 0;
        let len = listArray.length;
        for (let i=0; i<this.row; i++) {
            this.mtx[i] = [];
            for (let j=0; j<this.col; j++) {
                this.mtx[i][j] = listArray[offset];
                if (offset < len) {
                    this.mtx[i][j] = listArray[offset];
                } else {
                    this.mtx[i][j] = 0;
                }
                offset++;
            }
        }
    }
    add(mtx) {
        if (mtx.col !== this.col || mtx.row !== this.row) {
            console.error('col or row don\'t match');
            return;
        }
        for (let i=0; i<this.col; i++) {
            for (let j=0; i<this.row; j++) {
                this.mtx[i][j] += mtx[i][j];
            }
        }
        return this;
    }
    mul(mtx) {
        if (this.col !== mtx.row) {
            throw new Error('col or row don\'t match');
        }
        this.col = mtx.col;
        let new_mtx = [];
        for (let i=0; i<this.row; i++) {
            new_mtx[i] = [];
            for (let j=0; j<this.col; j++) {
                new_mtx[i][j] = _calMult(i, j, mtx.row, this.mtx, mtx.mtx);
            }
        }
        this.mtx = new_mtx;
        function _calMult(i,j,n,ml,mr) {
            let ret = 0;
            for (let x=0; x<n; x++) {
                ret += ml[i][x]*mr[x][j];
            }
            return ret;
        }
        return this;
    }
}
class Vector {
    constructor(x, y) {
        if (typeof x === 'object') {
            this.x = y.x - x.x;
            this.y = y.y - x.y;
        } else {
            this.x = x;
            this.y = y;
        }
    }
    cross(vec) {
        return this.x*vec.y - this.y*vec.x;
    }
    dot(vec) {
        return this.x*vec.x + this.y*vec.y;
    }
    mod() {
        return Math.sqrt(this.x*this.x + this.y*this.y);
    }
    normalize() {
        let mod = this.mod();
        return new Vector(this.x/mod, this.y/mod);
    }
    project(vec) {
        return vec.dot(this.normalize());
    }
}
class Segment {
    constructor(A, B) {
        this.s = A;
        this.e = B;
        this.vec = new Vector(B.x-A.x, B.y-A.y);
    }
    isCross(segment) {
        let vec1 = new Vector(segment.s.x - this.s.x, segment.s.y - this.s.y);
        let vec2 = new Vector(segment.e.x - this.s.x, segment.e.y - this.s.y);
        return this.vec.cross(vec1)*this.vec.cross(vec2) <= 0;
    }
}

var utils = {
    initCanvasContainer(id, width, height) {
        let _client_w = window.innerWidth || document.body.clientWidth || document.documentElement.clientWidth,
            _client_h = window.innerHeight || document.body.clientHeight || document.documentElement.clientHeight;
        _client_h -= 20;
        const ratio = 3/4;
        let _w = 0,_h = 0;
        if (_client_w/_client_h > ratio) {
            console.log(_client_h);
            _w = _client_h * ratio;
            _h = _client_h;
        } else {
            _w = _client_w;
            _h = _client_w / ratio;
        }
        let container = document.getElementById(id);
        container.setAttribute('style', 'width:'+ _w +'px;height:'+ _h +'px;');
        let cvs = document.createElement('canvas');
        cvs.setAttribute('style', 'padding:0;margin:0;width:100%;height:100%;background: #222;');
        container.append(cvs);
        cvs.width = width;
        cvs.height = height;
        return cvs;
    },
    rand(min, max) {
        return Math.floor(Math.random() * (max-min+1)) + min;
    },
    captureMouse: function(element) {
        const mouse = {
            x: 0,
            y: 0,
        };
        element.addEventListener('mousemove', (event) => {
            let x;
            let y;
            if (event.pageX || event.pageY) {
                x = event.pageX;
                y = event.pageY;
            } else {
                x = event.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
                y = event.clientY + document.body.scrollTop + document.documentElement.scrollTop;
            }
            x -= element.offsetLeft;
            y -= element.offsetTop;

            mouse.x = x;
            mouse.y = y;
        }, false);
        return mouse;
    },
    radToDeg(rad) {
        return rad*180/Math.PI;
    },
    hitTestVecPro(polygonA, polygonB) {
        for (let i of polygonA) {
            for (let j of polygonB) {
                if (i.isCross(j)) {
                    return true;
                }
            }
        }
        return false;
    },
    hitTestProject(polygonA, polygonB) {
        for (let i of polygonA) {

        }
    }
};
if(typeof exports !== 'undefined') {
    exports = utils;
}
