// from: left button corner
// to: top rigth corner
// click: where the click is 
function Within(from, to, click) {
    return click.x >= from.x && click.x <= to.x && click.y <= from.y && click.y >= to.y;
}

obj = {
    imgs: {
        history: document.getElementById('history'),
        login: document.getElementById('login'),
        menu: document.getElementById('menu'),
        qr_scanner: document.getElementById('qr_scanner'),
        qr_scanner_completed: document.getElementById('qr_scanner_completed'),
        statistics: document.getElementById('statistics'),
    },
    selected: null,
    menu_pre: null,
    Init: function() {
        document.addEventListener('click', this.Click, true);
        this.Show(this.imgs.login);
    },
    Click: function(e) {
        console.log(e);
        var click = { x: e.clientX, y: e.clientY };
        switch (obj.selected) {
            case obj.imgs.login:
                if (Within({ x: 61, y: 390 }, { x: 339, y: 354 }, click)) {
                    //login
                    obj.Show(obj.imgs.qr_scanner);
                }
                break;
            case obj.imgs.qr_scanner:
                if (Within({ x: 43, y: 126 }, { x: 88, y: 100 }, click)) {
                    //menu
                    obj.menu_pre = obj.selected;
                    obj.Show(obj.imgs.menu);
                } else if (Within({ x: 40, y: 574 }, { x: 352, y: 139 }, click)) {
                    //qr scanner completed
                    obj.Show(obj.imgs.qr_scanner_completed);
                }
                break;
            case obj.imgs.qr_scanner_completed:
                if (Within({ x: 30, y: 142 }, { x: 73, y: 105 }, click)) {
                    //menu
                    obj.menu_pre = obj.selected;
                    obj.Show(obj.imgs.menu);
                } else if (Within({ x: 26, y: 500 }, { x: 336, y: 147 }, click)) {
                    if (!Within({ x: 63, y: 434 }, { x: 309, y: 275 }, click)) {
                        //go back to qr scanner
                        obj.Show(obj.imgs.qr_scanner);
                    } else if (Within({ x: 126, y: 420 }, { x: 244, y: 400 }, click)) {
                        //show more information
                        window.alert('Show detail information');
                    }
                }
                break;
            case obj.imgs.menu:
                if (Within({ x: 234, y: 572 }, { x: 343, y: 97 }, click) || Within({ x: 32, y: 136 }, { x: 72, y: 102 }, click)) {
                    //pre page
                    obj.Show(obj.menu_pre);
                } else if (Within({ x: 31, y: 168 }, { x: 222, y: 143 }, click)) {
                    //goto qr scanner
                    obj.Show(obj.imgs.qr_scanner);
                } else if (Within({ x: 31, y: 204 }, { x: 222, y: 174 }, click)) {
                    //goto history
                    obj.Show(obj.imgs.history);
                } else if (Within({ x: 31, y: 237 }, { x: 222, y: 214 }, click)) {
                    //goto statistics
                    obj.Show(obj.imgs.statistics);
                } else if (Within({ x: 31, y: 511 }, { x: 222, y: 401 }, click)) {
                    //show about page
                    window.alert('Show about page');
                } else if (Within({ x: 31, y: 573 }, { x: 222, y: 529 }, click)) {
                    //goto login
                    obj.Show(obj.imgs.login);
                }
                break;
            case obj.imgs.history:
                if (Within({ x: 46, y: 150 }, { x: 97, y: 110 }, click)) {
                    obj.menu_pre = obj.selected;
                    obj.Show(obj.imgs.menu);
                }
                break;
            case obj.imgs.statistics:
                if (Within({ x: 33, y: 228 }, { x: 85, y: 107 }, click)) {
                    obj.menu_pre = obj.selected;
                    obj.Show(obj.imgs.menu);
                }
                break;
            default:
                break;
        }
        
    },
    Show: function(item) {
        for (i in this.imgs) {
            if (this.imgs[i] != item) {
                this.imgs[i].className = 'hidden';
            } else {
                this.imgs[i].className = '';
                this.selected = this.imgs[i];
            }
        }
    }
};

obj.Init();
