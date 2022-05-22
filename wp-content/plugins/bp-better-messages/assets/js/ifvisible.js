!function (t, e) {
    if ("object" == typeof exports && "object" == typeof module) module.exports = e(); else if ("function" == typeof define && define.amd) define([], e); else {
        var s = e();
        for (var i in s) ("object" == typeof exports ? exports : t)[i] = s[i]
    }
}(window, function () {
    return function (t) {
        var e = {};

        function s(i) {
            if (e[i]) return e[i].exports;
            var n = e[i] = {i: i, l: !1, exports: {}};
            return t[i].call(n.exports, n, n.exports, s), n.l = !0, n.exports
        }

        return s.m = t, s.c = e, s.d = function (t, e, i) {
            s.o(t, e) || Object.defineProperty(t, e, {enumerable: !0, get: i})
        }, s.r = function (t) {
            "undefined" != typeof Symbol && Symbol.toStringTag && Object.defineProperty(t, Symbol.toStringTag, {value: "Module"}), Object.defineProperty(t, "__esModule", {value: !0})
        }, s.t = function (t, e) {
            if (1 & e && (t = s(t)), 8 & e) return t;
            if (4 & e && "object" == typeof t && t && t.__esModule) return t;
            var i = Object.create(null);
            if (s.r(i), Object.defineProperty(i, "default", {
                enumerable: !0,
                value: t
            }), 2 & e && "string" != typeof t) for (var n in t) s.d(i, n, function (e) {
                return t[e]
            }.bind(null, n));
            return i
        }, s.n = function (t) {
            var e = t && t.__esModule ? function () {
                return t.default
            } : function () {
                return t
            };
            return s.d(e, "a", e), e
        }, s.o = function (t, e) {
            return Object.prototype.hasOwnProperty.call(t, e)
        }, s.p = "", s(s.s = 0)
    }({
        "./node_modules/webpack/buildin/global.js":
        /*!***********************************!*\
          !*** (webpack)/buildin/global.js ***!
          \***********************************/
        /*! no static exports found */function (t, e) {
            var s;
            s = function () {
                return this
            }();
            try {
                s = s || new Function("return this")()
            } catch (t) {
                "object" == typeof window && (s = window)
            }
            t.exports = s
        }, "./src/Events.ts":
        /*!***********************!*\
          !*** ./src/Events.ts ***!
          \***********************/
        /*! no static exports found */function (t, e, s) {
            "use strict";
            Object.defineProperty(e, "__esModule", {value: !0}), function (t) {
                const e = {};
                let s;
                t.attach = function (t, s) {
                    e[t] || (e[t] = []), e[t].push(s)
                }, t.fire = function (t, s = []) {
                    e[t] && e[t].forEach(t => {
                        t(...s)
                    })
                }, t.remove = function (t, s) {
                    s || delete e[t], e[t] && (e[t] = e[t].filter(t => s !== t))
                }, t.dom = function (t, e, i) {
                    return s || (s = t.addEventListener ? (t, e, s) => t.addEventListener(e, s, !1) : "function" == typeof t.attachEvent ? (t, e, s) => t.attachEvent(`on${e}`, s, !1) : (t, e, s) => t[`on${e}`] = s), s(t, e, i)
                }
            }(e.Events || (e.Events = {}))
        }, "./src/Timer.ts":
        /*!**********************!*\
          !*** ./src/Timer.ts ***!
          \**********************/
        /*! no static exports found */function (t, e, s) {
            "use strict";
            Object.defineProperty(e, "__esModule", {value: !0});
            const i = s(/*! ./ifvisible */"./src/ifvisible.ts");
            e.default = class {
                constructor(t, e, s) {
                    this.ifvisible = t, this.seconds = e, this.callback = s, this.stopped = !1, this.start(), this.ifvisible.on("statusChanged", t => {
                        !1 === this.stopped && (t.status === i.STATUS_ACTIVE ? this.start() : this.pause())
                    })
                }

                start() {
                    this.stopped = !1, clearInterval(this.token), this.token = setInterval(this.callback, 1e3 * this.seconds)
                }

                stop() {
                    this.stopped = !0, clearInterval(this.token)
                }

                resume() {
                    this.start()
                }

                pause() {
                    this.stop()
                }
            }
        }, "./src/ifvisible.ts":
        /*!**************************!*\
          !*** ./src/ifvisible.ts ***!
          \**************************/
        /*! no static exports found */function (t, e, s) {
            "use strict";
            Object.defineProperty(e, "__esModule", {value: !0});
            const i = s(/*! ./Events */"./src/Events.ts"), n = s(/*! ./Timer */"./src/Timer.ts");
            let o, r;
            e.STATUS_ACTIVE = "active", e.STATUS_IDLE = "idle", e.STATUS_HIDDEN = "hidden", e.IE = function () {
                let t = 3;
                const e = document.createElement("div"), s = e.getElementsByTagName("i");
                for (; e.innerHTML = `\x3c!--[if gt IE ${++t}]><i></i><![endif]--\x3e`, s[0];) ;
                return t > 4 ? t : void 0
            }();
            e.IfVisible = class {
                constructor(t, s) {
                    if (this.root = t, this.doc = s, this.status = e.STATUS_ACTIVE, this.VERSION = "2.0.11", this.timers = [], this.idleTime = 3e4, this.isLegacyModeOn = !1, void 0 !== this.doc.hidden ? (o = "hidden", r = "visibilitychange") : void 0 !== this.doc.mozHidden ? (o = "mozHidden", r = "mozvisibilitychange") : void 0 !== this.doc.msHidden ? (o = "msHidden", r = "msvisibilitychange") : void 0 !== this.doc.webkitHidden && (o = "webkitHidden", r = "webkitvisibilitychange"), void 0 === o) this.legacyMode(); else {
                        const t = () => {
                            this.doc[o] ? this.blur() : this.focus()
                        };
                        t(), i.Events.dom(this.doc, r, t)
                    }
                    this.startIdleTimer(), this.trackIdleStatus()
                }

                legacyMode() {
                    if (this.isLegacyModeOn) return;
                    let t = "blur";
                    e.IE < 9 && (t = "focusout"), i.Events.dom(this.root, t, () => this.blur()), i.Events.dom(this.root, "focus", () => this.focus()), this.isLegacyModeOn = !0
                }

                startIdleTimer(t) {
                    t instanceof MouseEvent && 0 === t.movementX && 0 === t.movementY || (this.timers.map(clearTimeout), this.timers.length = 0, this.status === e.STATUS_IDLE && this.wakeup(), this.idleStartedTime = +new Date, this.timers.push(setTimeout(() => {
                        if (this.status === e.STATUS_ACTIVE || this.status === e.STATUS_HIDDEN) return this.idle()
                    }, this.idleTime)))
                }

                trackIdleStatus() {
                    i.Events.dom(this.doc, "mousemove", this.startIdleTimer.bind(this)), i.Events.dom(this.doc, "mousedown", this.startIdleTimer.bind(this)), i.Events.dom(this.doc, "keyup", this.startIdleTimer.bind(this)), i.Events.dom(this.doc, "touchstart", this.startIdleTimer.bind(this)), i.Events.dom(this.root, "scroll", this.startIdleTimer.bind(this)), this.focus(this.startIdleTimer.bind(this))
                }

                on(t, e) {
                    return i.Events.attach(t, e), this
                }

                off(t, e) {
                    return i.Events.remove(t, e), this
                }

                setIdleDuration(t) {
                    return this.idleTime = 1e3 * t, this.startIdleTimer(), this
                }

                getIdleDuration() {
                    return this.idleTime
                }

                getIdleInfo() {
                    const t = +new Date;
                    let s;
                    if (this.status === e.STATUS_IDLE) s = {
                        isIdle: !0,
                        idleFor: t - this.idleStartedTime,
                        timeLeft: 0,
                        timeLeftPer: 100
                    }; else {
                        const e = this.idleStartedTime + this.idleTime - t;
                        s = {
                            isIdle: !1,
                            idleFor: t - this.idleStartedTime,
                            timeLeft: e,
                            timeLeftPer: parseFloat((100 - 100 * e / this.idleTime).toFixed(2))
                        }
                    }
                    return s
                }

                idle(t) {
                    return t ? this.on("idle", t) : (this.status = e.STATUS_IDLE, i.Events.fire("idle"), i.Events.fire("statusChanged", [{status: this.status}])), this
                }

                blur(t) {
                    return t ? this.on("blur", t) : (this.status = e.STATUS_HIDDEN, i.Events.fire("blur"), i.Events.fire("statusChanged", [{status: this.status}])), this
                }

                focus(t) {
                    return t ? this.on("focus", t) : this.status !== e.STATUS_ACTIVE && (this.status = e.STATUS_ACTIVE, i.Events.fire("focus"), i.Events.fire("wakeup"), i.Events.fire("statusChanged", [{status: this.status}])), this
                }

                wakeup(t) {
                    return t ? this.on("wakeup", t) : this.status !== e.STATUS_ACTIVE && (this.status = e.STATUS_ACTIVE, i.Events.fire("wakeup"), i.Events.fire("statusChanged", [{status: this.status}])), this
                }

                onEvery(t, e) {
                    return new n.default(this, t, e)
                }

                now(t) {
                    return void 0 !== t ? this.status === t : this.status === e.STATUS_ACTIVE
                }
            }
        }, "./src/main.ts":
        /*!*********************!*\
          !*** ./src/main.ts ***!
          \*********************/
        /*! no static exports found */function (t, e, s) {
            "use strict";
            (function (t) {
                Object.defineProperty(e, "__esModule", {value: !0});
                const i = s(/*! ./ifvisible */"./src/ifvisible.ts"),
                    n = "object" == typeof self && self.self === self && self || "object" == typeof t && t.global === t && t || this;
                e.ifvisible = new i.IfVisible(n, document)
            }).call(this, s(/*! ./../node_modules/webpack/buildin/global.js */"./node_modules/webpack/buildin/global.js"))
        }, 0:
        /*!***************************!*\
          !*** multi ./src/main.ts ***!
          \***************************/
        /*! no static exports found */function (t, e, s) {
            t.exports = s(/*! ./src/main.ts */"./src/main.ts")
        }
    })
});
