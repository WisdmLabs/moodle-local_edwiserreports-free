! function(t, e) {
    "object" == typeof exports && "undefined" != typeof module ? module.exports = e() : "function" == typeof define && define.amd ? define(e) : t.jspdf = e()
}(this, function() {
    "use strict";
    var t = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function(t) {
            return typeof t
        } : function(t) {
            return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : typeof t
        },
        e = (function() {
            function t(t) {
                this.value = t
            }

            function e(e) {
                function n(i, o) {
                    try {
                        var a = e[i](o),
                            s = a.value;
                        s instanceof t ? Promise.resolve(s.value).then(function(t) {
                            n("next", t)
                        }, function(t) {
                            n("throw", t)
                        }) : r(a.done ? "return" : "normal", a.value)
                    } catch (t) {
                        r("throw", t)
                    }
                }

                function r(t, e) {
                    switch (t) {
                        case "return":
                            i.resolve({
                                value: e,
                                done: !0
                            });
                            break;
                        case "throw":
                            i.reject(e);
                            break;
                        default:
                            i.resolve({
                                value: e,
                                done: !1
                            })
                    }(i = i.next) ? n(i.key, i.arg): o = null
                }
                var i, o;
                this._invoke = function(t, e) {
                    return new Promise(function(r, a) {
                        var s = {
                            key: t,
                            arg: e,
                            resolve: r,
                            reject: a,
                            next: null
                        };
                        o ? o = o.next = s : (i = o = s, n(t, e))
                    })
                }, "function" != typeof e.return && (this.return = void 0)
            }
            "function" == typeof Symbol && Symbol.asyncIterator && (e.prototype[Symbol.asyncIterator] = function() {
                return this
            }), e.prototype.next = function(t) {
                return this._invoke("next", t)
            }, e.prototype.throw = function(t) {
                return this._invoke("throw", t)
            }, e.prototype.return = function(t) {
                return this._invoke("return", t)
            }
        }(), function(e) {
            function n(t) {
                var n = {};
                this.subscribe = function(t, e, r) {
                    if ("function" != typeof e) return !1;
                    n.hasOwnProperty(t) || (n[t] = {});
                    var i = Math.random().toString(35);
                    return n[t][i] = [e, !!r], i
                }, this.unsubscribe = function(t) {
                    for (var e in n)
                        if (n[e][t]) return delete n[e][t], !0;
                    return !1
                }, this.publish = function(r) {
                    if (n.hasOwnProperty(r)) {
                        var i = Array.prototype.slice.call(arguments, 1),
                            o = [];
                        for (var a in n[r]) {
                            var s = n[r][a];
                            try {
                                s[0].apply(t, i)
                            } catch (t) {
                                e.console && console.error("jsPDF PubSub Error", t.message, t)
                            }
                            s[1] && o.push(a)
                        }
                        o.length && o.forEach(this.unsubscribe)
                    }
                }
            }

            function i(c, l, u, h) {
                var f = {};
                "object" === (void 0 === c ? "undefined" : t(c)) && (c = (f = c).orientation, l = f.unit || l, u = f.format || u, h = f.compress || f.compressPdf || h), l = l || "mm", u = u || "a4", c = ("" + (c || "P")).toLowerCase();
                ("" + u).toLowerCase();
                var d, p, m, g, w, y, v, b, x, k = !!h && "function" == typeof Uint8Array,
                    _ = f.textColor || "0 g",
                    S = f.drawColor || "0 G",
                    C = f.fontSize || 16,
                    A = f.lineHeight || 1.15,
                    q = f.lineWidth || .200025,
                    T = 2,
                    P = !1,
                    I = [],
                    E = {},
                    O = {},
                    B = 0,
                    F = [],
                    R = [],
                    D = [],
                    j = [],
                    N = [],
                    z = 0,
                    L = 0,
                    M = 0,
                    U = {
                        title: "",
                        subject: "",
                        author: "",
                        keywords: "",
                        creator: ""
                    },
                    H = {},
                    W = new n(H),
                    V = f.hotfixes || [],
                    X = function(t) {
                        return t.toFixed(2)
                    },
                    G = function(t) {
                        return t.toFixed(3)
                    },
                    Y = function(t) {
                        return ("0" + parseInt(t)).slice(-2)
                    },
                    J = function(t) {
                        P ? F[g].push(t) : (M += t.length + 1, j.push(t))
                    },
                    Q = function() {
                        return T++, I[T] = M, J(T + " 0 obj"), T
                    },
                    K = function(t) {
                        J("stream"), J(t), J("endstream")
                    },
                    $ = function() {
                        var t, n, r, a, s, c, l, u, h, f = [];
                        for (l = e.adler32cs || i.adler32cs, k && void 0 === l && (k = !1), t = 1; t <= B; t++) {
                            if (f.push(Q()), u = (w = D[t].width) * p, h = (y = D[t].height) * p, J("<</Type /Page"), J("/Parent 1 0 R"), J("/Resources 2 0 R"), J("/MediaBox [0 0 " + X(u) + " " + X(h) + "]"), W.publish("putPage", {
                                    pageNumber: t,
                                    page: F[t]
                                }), J("/Contents " + (T + 1) + " 0 R"), J(">>"), J("endobj"), n = F[t].join("\n"), Q(), k) {
                                for (r = [], a = n.length; a--;) r[a] = n.charCodeAt(a);
                                c = l.from(n), (s = new o(6)).append(new Uint8Array(r)), n = s.flush(), (r = new Uint8Array(n.length + 6)).set(new Uint8Array([120, 156])), r.set(n, 2), r.set(new Uint8Array([255 & c, c >> 8 & 255, c >> 16 & 255, c >> 24 & 255]), n.length + 2), n = String.fromCharCode.apply(null, r), J("<</Length " + n.length + " /Filter [/FlateDecode]>>")
                            } else J("<</Length " + n.length + ">>");
                            K(n), J("endobj")
                        }
                        I[1] = M, J("1 0 obj"), J("<</Type /Pages");
                        var d = "/Kids [";
                        for (a = 0; a < B; a++) d += f[a] + " 0 R ";
                        J(d + "]"), J("/Count " + B), J(">>"), J("endobj"), W.publish("postPutPages")
                    },
                    Z = function(t) {
                        t.objectNumber = Q(), J("<</BaseFont/" + t.PostScriptName + "/Type/Font"), "string" == typeof t.encoding && J("/Encoding/" + t.encoding), J("/Subtype/Type1>>"), J("endobj")
                    },
                    tt = function() {
                        for (var t in E) E.hasOwnProperty(t) && Z(E[t])
                    },
                    et = function() {
                        W.publish("putXobjectDict")
                    },
                    nt = function() {
                        J("/ProcSet [/PDF /Text /ImageB /ImageC /ImageI]"), J("/Font <<");
                        for (var t in E) E.hasOwnProperty(t) && J("/" + t + " " + E[t].objectNumber + " 0 R");
                        J(">>"), J("/XObject <<"), et(), J(">>")
                    },
                    rt = function() {
                        tt(), W.publish("putResources"), I[2] = M, J("2 0 obj"), J("<<"), nt(), J(">>"), J("endobj"), W.publish("postPutResources")
                    },
                    it = function() {
                        W.publish("putAdditionalObjects");
                        for (var t = 0; t < N.length; t++) {
                            var e = N[t];
                            I[e.objId] = M, J(e.objId + " 0 obj"), J(e.content), J("endobj")
                        }
                        T += N.length, W.publish("postPutAdditionalObjects")
                    },
                    ot = function(t, e, n) {
                        O.hasOwnProperty(e) || (O[e] = {}), O[e][n] = t
                    },
                    at = function(t, e, n, r) {
                        var i = "F" + (Object.keys(E).length + 1).toString(10),
                            o = E[i] = {
                                id: i,
                                PostScriptName: t,
                                fontName: e,
                                fontStyle: n,
                                encoding: r,
                                metadata: {}
                            };
                        return ot(i, e, n), W.publish("addFont", o), i
                    },
                    st = function(t, e) {
                        var n, r, i, o, a, s, c, l, u;
                        if (e = e || {}, i = e.sourceEncoding || "Unicode", a = e.outputEncoding, (e.autoencode || a) && E[d].metadata && E[d].metadata[i] && E[d].metadata[i].encoding && (o = E[d].metadata[i].encoding, !a && E[d].encoding && (a = E[d].encoding), !a && o.codePages && (a = o.codePages[0]), "string" == typeof a && (a = o[a]), a)) {
                            for (c = !1, s = [], n = 0, r = t.length; n < r; n++)(l = a[t.charCodeAt(n)]) ? s.push(String.fromCharCode(l)) : s.push(t[n]), s[n].charCodeAt(0) >> 8 && (c = !0);
                            t = s.join("")
                        }
                        for (n = t.length; void 0 === c && 0 !== n;) t.charCodeAt(n - 1) >> 8 && (c = !0), n--;
                        if (!c) return t;
                        for (s = e.noBOM ? [] : [254, 255], n = 0, r = t.length; n < r; n++) {
                            if (l = t.charCodeAt(n), (u = l >> 8) >> 8) throw new Error("Character at position " + n + " of string '" + t + "' exceeds 16bits. Cannot be encoded into UCS-2 BE");
                            s.push(u), s.push(l - (u << 8))
                        }
                        return String.fromCharCode.apply(void 0, s)
                    },
                    ct = function(t, e) {
                        return st(t, e).replace(/\\/g, "\\\\").replace(/\(/g, "\\(").replace(/\)/g, "\\)")
                    },
                    lt = function() {
                        J("/Producer (jsPDF " + i.version + ")");
                        for (var t in U) U.hasOwnProperty(t) && U[t] && J("/" + t.substr(0, 1).toUpperCase() + t.substr(1) + " (" + ct(U[t]) + ")");
                        var e = new Date,
                            n = e.getTimezoneOffset(),
                            r = n < 0 ? "+" : "-",
                            o = Math.floor(Math.abs(n / 60)),
                            a = Math.abs(n % 60),
                            s = [r, Y(o), "'", Y(a), "'"].join("");
                        J(["/CreationDate (D:", e.getFullYear(), Y(e.getMonth() + 1), Y(e.getDate()), Y(e.getHours()), Y(e.getMinutes()), Y(e.getSeconds()), s, ")"].join(""))
                    },
                    ut = function() {
                        switch (J("/Type /Catalog"), J("/Pages 1 0 R"), b || (b = "fullwidth"), b) {
                            case "fullwidth":
                                J("/OpenAction [3 0 R /FitH null]");
                                break;
                            case "fullheight":
                                J("/OpenAction [3 0 R /FitV null]");
                                break;
                            case "fullpage":
                                J("/OpenAction [3 0 R /Fit]");
                                break;
                            case "original":
                                J("/OpenAction [3 0 R /XYZ null null 1]");
                                break;
                            default:
                                var t = "" + b;
                                "%" === t.substr(t.length - 1) && (b = parseInt(b) / 100), "number" == typeof b && J("/OpenAction [3 0 R /XYZ null null " + X(b) + "]")
                        }
                        switch (x || (x = "continuous"), x) {
                            case "continuous":
                                J("/PageLayout /OneColumn");
                                break;
                            case "single":
                                J("/PageLayout /SinglePage");
                                break;
                            case "two":
                            case "twoleft":
                                J("/PageLayout /TwoColumnLeft");
                                break;
                            case "tworight":
                                J("/PageLayout /TwoColumnRight")
                        }
                        v && J("/PageMode /" + v), W.publish("putCatalog")
                    },
                    ht = function() {
                        J("/Size " + (T + 1)), J("/Root " + T + " 0 R"), J("/Info " + (T - 1) + " 0 R")
                    },
                    ft = function(t, e) {
                        var n = "string" == typeof e && e.toLowerCase();
                        if ("string" == typeof t) {
                            var r = t.toLowerCase();
                            s.hasOwnProperty(r) && (t = s[r][0] / p, e = s[r][1] / p)
                        }
                        if (Array.isArray(t) && (e = t[1], t = t[0]), n) {
                            switch (n.substr(0, 1)) {
                                case "l":
                                    e > t && (n = "s");
                                    break;
                                case "p":
                                    t > e && (n = "s")
                            }
                            "s" === n && (m = t, t = e, e = m)
                        }
                        P = !0, F[++B] = [], D[B] = {
                            width: Number(t) || w,
                            height: Number(e) || y
                        }, R[B] = {}, mt(B)
                    },
                    dt = function() {
                        ft.apply(this, arguments), J(X(q * p) + " w"), J(S), 0 !== z && J(z + " J"), 0 !== L && J(L + " j"), W.publish("addPage", {
                            pageNumber: B
                        })
                    },
                    pt = function(t) {
                        t > 0 && t <= B && (F.splice(t, 1), D.splice(t, 1), g > --B && (g = B), this.setPage(g))
                    },
                    mt = function(t) {
                        t > 0 && t <= B && (g = t, w = D[t].width, y = D[t].height)
                    },
                    gt = function(t, e) {
                        var n;
                        switch (t = void 0 !== t ? t : E[d].fontName, e = void 0 !== e ? e : E[d].fontStyle, void 0 !== t && (t = t.toLowerCase()), t) {
                            case "sans-serif":
                            case "verdana":
                            case "arial":
                            case "helvetica":
                                t = "helvetica";
                                break;
                            case "fixed":
                            case "monospace":
                            case "terminal":
                            case "courier":
                                t = "courier";
                                break;
                            case "serif":
                            case "cursive":
                            case "fantasy":
                            default:
                                t = "times"
                        }
                        try {
                            n = O[t][e]
                        } catch (t) {}
                        return n || null == (n = O.times[e]) && (n = O.times.normal), n
                    },
                    wt = function() {
                        P = !1, T = 2, M = 0, j = [], I = [], N = [], W.publish("buildDocument"), J("%PDF-" + a), $(), it(), rt(), Q(), J("<<"), lt(), J(">>"), J("endobj"), Q(), J("<<"), ut(), J(">>"), J("endobj");
                        var t, e = M,
                            n = "0000000000";
                        for (J("xref"), J("0 " + (T + 1)), J(n + " 65535 f "), t = 1; t <= T; t++) {
                            var r = I[t];
                            J("function" == typeof r ? (n + I[t]()).slice(-10) + " 00000 n " : (n + I[t]).slice(-10) + " 00000 n ")
                        }
                        return J("trailer"), J("<<"), ht(), J(">>"), J("startxref"), J("" + e), J("%%EOF"), P = !0, j.join("\n")
                    },
                    yt = function(t) {
                        var e = "S";
                        return "F" === t ? e = "f" : "FD" === t || "DF" === t ? e = "B" : "f" !== t && "f*" !== t && "B" !== t && "B*" !== t || (e = t), e
                    },
                    vt = function() {
                        for (var t = wt(), e = t.length, n = new ArrayBuffer(e), r = new Uint8Array(n); e--;) r[e] = t.charCodeAt(e);
                        return n
                    },
                    bt = function() {
                        return new Blob([vt()], {
                            type: "application/pdf"
                        })
                    },
                    xt = function(t) {
                        return t.foo = function() {
                            try {
                                return t.apply(this, arguments)
                            } catch (t) {
                                var n = t.stack || "";
                                ~n.indexOf(" at ") && (n = n.split(" at ")[1]);
                                var r = "Error in function " + n.split("\n")[0].split("<")[0] + ": " + t.message;
                                if (!e.console) throw new Error(r);
                                e.console.error(r, t), e.alert && alert(r)
                            }
                        }, t.foo.bar = t, t.foo
                    }(function(t, n) {
                        var i = "dataur" === ("" + t).substr(0, 6) ? "data:application/pdf;base64," + btoa(wt()) : 0;
                        switch (t) {
                            case void 0:
                                return wt();
                            case "save":
                                if (navigator.getUserMedia && (void 0 === e.URL || void 0 === e.URL.createObjectURL)) return H.output("dataurlnewwindow");
                                r(bt(), n), "function" == typeof r.unload && e.setTimeout && setTimeout(r.unload, 911);
                                break;
                            case "arraybuffer":
                                return vt();
                            case "blob":
                                return bt();
                            case "bloburi":
                            case "bloburl":
                                return e.URL && e.URL.createObjectURL(bt()) || void 0;
                            case "datauristring":
                            case "dataurlstring":
                                return i;
                            case "dataurlnewwindow":
                                var o = e.open(i);
                                if (o || "undefined" == typeof safari) return o;
                            case "datauri":
                            case "dataurl":
                                return e.document.location.href = i;
                            default:
                                throw new Error('Output type "' + t + '" is not supported.')
                        }
                    }),
                    kt = function(t) {
                        return !0 === Array.isArray(V) && V.indexOf(t) > -1
                    };
                switch (l) {
                    case "pt":
                        p = 1;
                        break;
                    case "mm":
                        p = 72 / 25.4000508;
                        break;
                    case "cm":
                        p = 72 / 2.54000508;
                        break;
                    case "in":
                        p = 72;
                        break;
                    case "px":
                        p = 1 == kt("px_scaling") ? .75 : 96 / 72;
                        break;
                    case "pc":
                    case "em":
                        p = 12;
                        break;
                    case "ex":
                        p = 6;
                        break;
                    default:
                        throw "Invalid unit: " + l
                }
                H.internal = {
                    pdfEscape: ct,
                    getStyle: yt,
                    getFont: function() {
                        return E[gt.apply(H, arguments)]
                    },
                    getFontSize: function() {
                        return C
                    },
                    getLineHeight: function() {
                        return C * A
                    },
                    write: function(t) {
                        J(1 === arguments.length ? t : Array.prototype.join.call(arguments, " "))
                    },
                    getCoordinateString: function(t) {
                        return X(t * p)
                    },
                    getVerticalCoordinateString: function(t) {
                        return X((y - t) * p)
                    },
                    collections: {},
                    newObject: Q,
                    newAdditionalObject: function() {
                        var t = 2 * F.length + 1,
                            e = {
                                objId: t += N.length,
                                content: ""
                            };
                        return N.push(e), e
                    },
                    newObjectDeferred: function() {
                        return T++, I[T] = function() {
                            return M
                        }, T
                    },
                    newObjectDeferredBegin: function(t) {
                        I[t] = M
                    },
                    putStream: K,
                    events: W,
                    scaleFactor: p,
                    pageSize: {get width() {
                            return w
                        },
                        get height() {
                            return y
                        }
                    },
                    output: function(t, e) {
                        return xt(t, e)
                    },
                    getNumberOfPages: function() {
                        return F.length - 1
                    },
                    pages: F,
                    out: J,
                    f2: X,
                    getPageInfo: function(t) {
                        return {
                            objId: 2 * (t - 1) + 3,
                            pageNumber: t,
                            pageContext: R[t]
                        }
                    },
                    getCurrentPageInfo: function() {
                        return {
                            objId: 2 * (g - 1) + 3,
                            pageNumber: g,
                            pageContext: R[g]
                        }
                    },
                    getPDFVersion: function() {
                        return a
                    },
                    hasHotfix: kt
                }, H.addPage = function() {
                    return dt.apply(this, arguments), this
                }, H.setPage = function() {
                    return mt.apply(this, arguments), this
                }, H.insertPage = function(t) {
                    return this.addPage(), this.movePage(g, t), this
                }, H.movePage = function(t, e) {
                    if (t > e) {
                        for (var n = F[t], r = D[t], i = R[t], o = t; o > e; o--) F[o] = F[o - 1], D[o] = D[o - 1], R[o] = R[o - 1];
                        F[e] = n, D[e] = r, R[e] = i, this.setPage(e)
                    } else if (t < e) {
                        for (var n = F[t], r = D[t], i = R[t], o = t; o < e; o++) F[o] = F[o + 1], D[o] = D[o + 1], R[o] = R[o + 1];
                        F[e] = n, D[e] = r, R[e] = i, this.setPage(e)
                    }
                    return this
                }, H.deletePage = function() {
                    return pt.apply(this, arguments), this
                }, H.setDisplayMode = function(t, e, n) {
                    if (b = t, x = e, v = n, -1 == [void 0, null, "UseNone", "UseOutlines", "UseThumbs", "FullScreen"].indexOf(n)) throw new Error('Page mode must be one of UseNone, UseOutlines, UseThumbs, or FullScreen. "' + n + '" is not recognized.');
                    return this
                }, H.text = function(t, e, n, r, i, o) {
                    function a(t) {
                        return t = t.split("\t").join(Array(f.TabLen || 9).join(" ")), ct(t, r)
                    }
                    "number" == typeof t && (m = n, n = e, e = t, t = m), "string" == typeof t && (t = t.match(/[\n\r]/) ? t.split(/\r\n|\r|\n/g) : [t]), "string" == typeof i && (o = i, i = null), "string" == typeof r && (o = r, r = null), "number" == typeof r && (i = r, r = null);
                    var s = "",
                        c = "Td";
                    if (i) {
                        i *= Math.PI / 180;
                        var l = Math.cos(i),
                            u = Math.sin(i);
                        s = [X(l), X(u), X(-1 * u), X(l), ""].join(" "), c = "Tm"
                    }
                    "noBOM" in (r = r || {}) || (r.noBOM = !0), "autoencode" in r || (r.autoencode = !0);
                    var h = "",
                        g = this.internal.getCurrentPageInfo().pageContext;
                    if (!0 === r.stroke ? !0 !== g.lastTextWasStroke && (h = "1 Tr\n", g.lastTextWasStroke = !0) : (g.lastTextWasStroke && (h = "0 Tr\n"), g.lastTextWasStroke = !1), void 0 === this._runningPageHeight && (this._runningPageHeight = 0), "string" == typeof t) t = a(t);
                    else {
                        if ("[object Array]" !== Object.prototype.toString.call(t)) throw new Error('Type of text must be string or Array. "' + t + '" is not recognized.');
                        for (var w = t.concat(), v = [], b = w.length; b--;) v.push(a(w.shift()));
                        if (o) {
                            var x, k, S, q = C * A,
                                T = t.map(function(t) {
                                    return this.getStringUnitWidth(t) * C / p
                                }, this);
                            if (S = Math.max.apply(Math, T), "center" === o) x = e - S / 2, e -= T[0] / 2;
                            else {
                                if ("right" !== o) throw new Error('Unrecognized alignment option, use "center" or "right".');
                                x = e - S, e -= T[0]
                            }
                            k = e, t = v[0];
                            for (var P = 1, b = v.length; P < b; P++) {
                                var I = S - T[P];
                                "center" === o && (I /= 2), t += ") Tj\n" + (x - k + I) + " -" + q + " Td (" + v[P], k = x + I
                            }
                        } else t = v.join(") Tj\nT* (")
                    }
                    var E;
                    return E = X((y - n) * p), J("BT\n/" + d + " " + C + " Tf\n" + C * A + " TL\n" + h + _ + "\n" + s + X(e * p) + " " + E + " " + c + "\n(" + t + ") Tj\nET"), this
                }, H.lstext = function(t, e, n, r) {
                    console.warn("jsPDF.lstext is deprecated");
                    for (var i = 0, o = t.length; i < o; i++, e += r) this.text(t[i], e, n);
                    return this
                }, H.line = function(t, e, n, r) {
                    return this.lines([
                        [n - t, r - e]
                    ], t, e)
                }, H.clip = function() {
                    J("W"), J("S")
                }, H.clip_fixed = function(t) {
                    J("evenodd" === t ? "W*" : "W"), J("n")
                }, H.lines = function(t, e, n, r, i, o) {
                    var a, s, c, l, u, h, f, d, g, w, v;
                    for ("number" == typeof t && (m = n, n = e, e = t, t = m), r = r || [1, 1], J(G(e * p) + " " + G((y - n) * p) + " m "), a = r[0], s = r[1], l = t.length, w = e, v = n, c = 0; c < l; c++) 2 === (u = t[c]).length ? (w = u[0] * a + w, v = u[1] * s + v, J(G(w * p) + " " + G((y - v) * p) + " l")) : (h = u[0] * a + w, f = u[1] * s + v, d = u[2] * a + w, g = u[3] * s + v, w = u[4] * a + w, v = u[5] * s + v, J(G(h * p) + " " + G((y - f) * p) + " " + G(d * p) + " " + G((y - g) * p) + " " + G(w * p) + " " + G((y - v) * p) + " c"));
                    return o && J(" h"), null !== i && J(yt(i)), this
                }, H.rect = function(t, e, n, r, i) {
                    yt(i);
                    return J([X(t * p), X((y - e) * p), X(n * p), X(-r * p), "re"].join(" ")), null !== i && J(yt(i)), this
                }, H.triangle = function(t, e, n, r, i, o, a) {
                    return this.lines([
                        [n - t, r - e],
                        [i - n, o - r],
                        [t - i, e - o]
                    ], t, e, [1, 1], a, !0), this
                }, H.roundedRect = function(t, e, n, r, i, o, a) {
                    var s = 4 / 3 * (Math.SQRT2 - 1);
                    return this.lines([
                        [n - 2 * i, 0],
                        [i * s, 0, i, o - o * s, i, o],
                        [0, r - 2 * o],
                        [0, o * s, -i * s, o, -i, o],
                        [2 * i - n, 0],
                        [-i * s, 0, -i, -o * s, -i, -o],
                        [0, 2 * o - r],
                        [0, -o * s, i * s, -o, i, -o]
                    ], t + i, e, [1, 1], a), this
                }, H.ellipse = function(t, e, n, r, i) {
                    var o = 4 / 3 * (Math.SQRT2 - 1) * n,
                        a = 4 / 3 * (Math.SQRT2 - 1) * r;
                    return J([X((t + n) * p), X((y - e) * p), "m", X((t + n) * p), X((y - (e - a)) * p), X((t + o) * p), X((y - (e - r)) * p), X(t * p), X((y - (e - r)) * p), "c"].join(" ")), J([X((t - o) * p), X((y - (e - r)) * p), X((t - n) * p), X((y - (e - a)) * p), X((t - n) * p), X((y - e) * p), "c"].join(" ")), J([X((t - n) * p), X((y - (e + a)) * p), X((t - o) * p), X((y - (e + r)) * p), X(t * p), X((y - (e + r)) * p), "c"].join(" ")), J([X((t + o) * p), X((y - (e + r)) * p), X((t + n) * p), X((y - (e + a)) * p), X((t + n) * p), X((y - e) * p), "c"].join(" ")), null !== i && J(yt(i)), this
                }, H.circle = function(t, e, n, r) {
                    return this.ellipse(t, e, n, n, r)
                }, H.setProperties = function(t) {
                    for (var e in U) U.hasOwnProperty(e) && t[e] && (U[e] = t[e]);
                    return this
                }, H.setFontSize = function(t) {
                    return C = t, this
                }, H.setFont = function(t, e) {
                    return d = gt(t, e), this
                }, H.setFontStyle = H.setFontType = function(t) {
                    return d = gt(void 0, t), this
                }, H.getFontList = function() {
                    var t, e, n, r = {};
                    for (t in O)
                        if (O.hasOwnProperty(t)) {
                            r[t] = n = [];
                            for (e in O[t]) O[t].hasOwnProperty(e) && n.push(e)
                        }
                    return r
                }, H.addFont = function(t, e, n) {
                    at(t, e, n, "StandardEncoding")
                }, H.setLineWidth = function(t) {
                    return J((t * p).toFixed(2) + " w"), this
                }, H.setDrawColor = function(t, e, n, r) {
                    var i;
                    return i = void 0 === e || void 0 === r && t === e === n ? "string" == typeof t ? t + " G" : X(t / 255) + " G" : void 0 === r ? "string" == typeof t ? [t, e, n, "RG"].join(" ") : [X(t / 255), X(e / 255), X(n / 255), "RG"].join(" ") : "string" == typeof t ? [t, e, n, r, "K"].join(" ") : [X(t), X(e), X(n), X(r), "K"].join(" "), J(i), this
                }, H.setFillColor = function(e, n, r, i) {
                    var o;
                    return void 0 === n || void 0 === i && e === n === r ? o = "string" == typeof e ? e + " g" : X(e / 255) + " g" : void 0 === i || "object" === (void 0 === i ? "undefined" : t(i)) ? (o = "string" == typeof e ? [e, n, r, "rg"].join(" ") : [X(e / 255), X(n / 255), X(r / 255), "rg"].join(" "), i && 0 === i.a && (o = ["255", "255", "255", "rg"].join(" "))) : o = "string" == typeof e ? [e, n, r, i, "k"].join(" ") : [X(e), X(n), X(r), X(i), "k"].join(" "), J(o), this
                }, H.setTextColor = function(t, e, n) {
                    if ("string" == typeof t && /^#[0-9A-Fa-f]{6}$/.test(t)) {
                        var r = parseInt(t.substr(1), 16);
                        t = r >> 16 & 255, e = r >> 8 & 255, n = 255 & r
                    }
                    return _ = 0 === t && 0 === e && 0 === n || void 0 === e ? G(t / 255) + " g" : [G(t / 255), G(e / 255), G(n / 255), "rg"].join(" "), this
                }, H.CapJoinStyles = {
                    0: 0,
                    butt: 0,
                    but: 0,
                    miter: 0,
                    1: 1,
                    round: 1,
                    rounded: 1,
                    circle: 1,
                    2: 2,
                    projecting: 2,
                    project: 2,
                    square: 2,
                    bevel: 2
                }, H.setLineCap = function(t) {
                    var e = this.CapJoinStyles[t];
                    if (void 0 === e) throw new Error("Line cap style of '" + t + "' is not recognized. See or extend .CapJoinStyles property for valid styles");
                    return z = e, J(e + " J"), this
                }, H.setLineJoin = function(t) {
                    var e = this.CapJoinStyles[t];
                    if (void 0 === e) throw new Error("Line join style of '" + t + "' is not recognized. See or extend .CapJoinStyles property for valid styles");
                    return L = e, J(e + " j"), this
                }, H.output = xt, H.save = function(t) {
                    H.output("save", t)
                };
                for (var _t in i.API) i.API.hasOwnProperty(_t) && ("events" === _t && i.API.events.length ? function(t, e) {
                    var n, r, i;
                    for (i = e.length - 1; - 1 !== i; i--) n = e[i][0], r = e[i][1], t.subscribe.apply(t, [n].concat("function" == typeof r ? [r] : r))
                }(W, i.API.events) : H[_t] = i.API[_t]);
                return function() {
                    for (var t = [
                            ["Helvetica", "helvetica", "normal"],
                            ["Helvetica-Bold", "helvetica", "bold"],
                            ["Helvetica-Oblique", "helvetica", "italic"],
                            ["Helvetica-BoldOblique", "helvetica", "bolditalic"],
                            ["Courier", "courier", "normal"],
                            ["Courier-Bold", "courier", "bold"],
                            ["Courier-Oblique", "courier", "italic"],
                            ["Courier-BoldOblique", "courier", "bolditalic"],
                            ["Times-Roman", "times", "normal"],
                            ["Times-Bold", "times", "bold"],
                            ["Times-Italic", "times", "italic"],
                            ["Times-BoldItalic", "times", "bolditalic"],
                            ["ZapfDingbats", "zapfdingbats"]
                        ], e = 0, n = t.length; e < n; e++) {
                        var r = at(t[e][0], t[e][1], t[e][2], "StandardEncoding"),
                            i = t[e][0].split("-");
                        ot(r, i[0], i[1] || "")
                    }
                    W.publish("addFonts", {
                        fonts: E,
                        dictionary: O
                    })
                }(), d = "F1", dt(u, c), W.publish("initialized"), H
            }
            var a = "1.3",
                s = {
                    a0: [2383.94, 3370.39],
                    a1: [1683.78, 2383.94],
                    a2: [1190.55, 1683.78],
                    a3: [841.89, 1190.55],
                    a4: [595.28, 841.89],
                    a5: [419.53, 595.28],
                    a6: [297.64, 419.53],
                    a7: [209.76, 297.64],
                    a8: [147.4, 209.76],
                    a9: [104.88, 147.4],
                    a10: [73.7, 104.88],
                    b0: [2834.65, 4008.19],
                    b1: [2004.09, 2834.65],
                    b2: [1417.32, 2004.09],
                    b3: [1000.63, 1417.32],
                    b4: [708.66, 1000.63],
                    b5: [498.9, 708.66],
                    b6: [354.33, 498.9],
                    b7: [249.45, 354.33],
                    b8: [175.75, 249.45],
                    b9: [124.72, 175.75],
                    b10: [87.87, 124.72],
                    c0: [2599.37, 3676.54],
                    c1: [1836.85, 2599.37],
                    c2: [1298.27, 1836.85],
                    c3: [918.43, 1298.27],
                    c4: [649.13, 918.43],
                    c5: [459.21, 649.13],
                    c6: [323.15, 459.21],
                    c7: [229.61, 323.15],
                    c8: [161.57, 229.61],
                    c9: [113.39, 161.57],
                    c10: [79.37, 113.39],
                    dl: [311.81, 623.62],
                    letter: [612, 792],
                    "government-letter": [576, 756],
                    legal: [612, 1008],
                    "junior-legal": [576, 360],
                    ledger: [1224, 792],
                    tabloid: [792, 1224],
                    "credit-card": [153, 243]
                };
            return i.API = {
                events: []
            }, i.version = "1.x-master", "function" == typeof define && define.amd ? define("jsPDF", function() {
                return i
            }) : "undefined" != typeof module && module.exports ? module.exports = i : e.jsPDF = i, i
        }("undefined" != typeof self && self || "undefined" != typeof window && window || void 0));
    /**
     * jsPDF AcroForm Plugin
     * Copyright (c) 2016 Alexander Weidt, https://github.com/BiggA94
     *
     * Licensed under the MIT License.
     * http://opensource.org/licenses/mit-license
     */
    (window.AcroForm = function(t) {
        var n = window.AcroForm;
        n.scale = function(t) {
            return t * (r.internal.scaleFactor / 1)
        }, n.antiScale = function(t) {
            return 1 / r.internal.scaleFactor * t
        };
        var r = {
            fields: [],
            xForms: [],
            acroFormDictionaryRoot: null,
            printedOut: !1,
            internal: null
        };
        e.API.acroformPlugin = r;
        var i = function() {
                for (var t in this.acroformPlugin.acroFormDictionaryRoot.Fields) {
                    var e = this.acroformPlugin.acroFormDictionaryRoot.Fields[t];
                    e.hasAnnotation && a.call(this, e)
                }
            },
            o = function() {
                if (this.acroformPlugin.acroFormDictionaryRoot) throw new Error("Exception while creating AcroformDictionary");
                this.acroformPlugin.acroFormDictionaryRoot = new n.AcroFormDictionary, this.acroformPlugin.internal = this.internal, this.acroformPlugin.acroFormDictionaryRoot._eventID = this.internal.events.subscribe("postPutResources", l), this.internal.events.subscribe("buildDocument", i), this.internal.events.subscribe("putCatalog", c), this.internal.events.subscribe("postPutPages", u)
            },
            a = function(t) {
                var n = {
                    type: "reference",
                    object: t
                };
                e.API.annotationPlugin.annotations[this.internal.getPageInfo(t.page).pageNumber].push(n)
            },
            s = function(t) {
                this.acroformPlugin.printedOut && (this.acroformPlugin.printedOut = !1, this.acroformPlugin.acroFormDictionaryRoot = null), this.acroformPlugin.acroFormDictionaryRoot || o.call(this), this.acroformPlugin.acroFormDictionaryRoot.Fields.push(t)
            },
            c = function() {
                void 0 !== this.acroformPlugin.acroFormDictionaryRoot ? this.internal.write("/AcroForm " + this.acroformPlugin.acroFormDictionaryRoot.objId + " 0 R") : console.log("Root missing...")
            },
            l = function() {
                this.internal.events.unsubscribe(this.acroformPlugin.acroFormDictionaryRoot._eventID), delete this.acroformPlugin.acroFormDictionaryRoot._eventID, this.acroformPlugin.printedOut = !0
            },
            u = function(t) {
                var e = !t;
                t || (this.internal.newObjectDeferredBegin(this.acroformPlugin.acroFormDictionaryRoot.objId), this.internal.out(this.acroformPlugin.acroFormDictionaryRoot.getString()));
                var t = t || this.acroformPlugin.acroFormDictionaryRoot.Kids;
                for (var r in t) {
                    var i = t[r],
                        o = i.Rect;
                    i.Rect && (i.Rect = n.internal.calculateCoordinates.call(this, i.Rect)), this.internal.newObjectDeferredBegin(i.objId);
                    var a = "";
                    if (a += i.objId + " 0 obj\n", a += "<<\n" + i.getContent(), i.Rect = o, i.hasAppearanceStream && !i.appearanceStreamContent) {
                        var s = n.internal.calculateAppearanceStream.call(this, i);
                        a += "/AP << /N " + s + " >>\n", this.acroformPlugin.xForms.push(s)
                    }
                    if (i.appearanceStreamContent) {
                        a += "/AP << ";
                        for (var c in i.appearanceStreamContent) {
                            var l = i.appearanceStreamContent[c];
                            if (a += "/" + c + " ", a += "<< ", Object.keys(l).length >= 1 || Array.isArray(l))
                                for (var r in l) "function" == typeof(u = l[r]) && (u = u.call(this, i)), a += "/" + r + " " + u + " ", this.acroformPlugin.xForms.indexOf(u) >= 0 || this.acroformPlugin.xForms.push(u);
                            else {
                                var u = l;
                                "function" == typeof u && (u = u.call(this, i)), a += "/" + r + " " + u + " \n", this.acroformPlugin.xForms.indexOf(u) >= 0 || this.acroformPlugin.xForms.push(u)
                            }
                            a += " >>\n"
                        }
                        a += ">>\n"
                    }
                    a += ">>\nendobj\n", this.internal.out(a)
                }
                e && h.call(this, this.acroformPlugin.xForms)
            },
            h = function(t) {
                for (var e in t) {
                    var n = e,
                        r = t[e];
                    this.internal.newObjectDeferredBegin(r && r.objId);
                    var i = "";
                    i += r ? r.getString() : "", this.internal.out(i), delete t[n]
                }
            };
        t.addField = function(t) {
            return t instanceof n.TextField ? d.call(this, t) : t instanceof n.ChoiceField ? p.call(this, t) : t instanceof n.Button ? f.call(this, t) : t instanceof n.ChildClass ? s.call(this, t) : t && s.call(this, t), t.page = this.acroformPlugin.internal.getCurrentPageInfo().pageNumber, this
        };
        var f = function(t) {
                (t = t || new n.Field).FT = "/Btn";
                var e = t.Ff || 0;
                t.pushbutton && (e = n.internal.setBitPosition(e, 17), delete t.pushbutton), t.radio && (e = n.internal.setBitPosition(e, 16), delete t.radio), t.noToggleToOff && (e = n.internal.setBitPosition(e, 15)), t.Ff = e, s.call(this, t)
            },
            d = function(t) {
                (t = t || new n.Field).FT = "/Tx";
                var e = t.Ff || 0;
                t.multiline && (e |= 4096), t.password && (e |= 8192), t.fileSelect && (e |= 1 << 20), t.doNotSpellCheck && (e |= 1 << 22), t.doNotScroll && (e |= 1 << 23), t.Ff = t.Ff || e, s.call(this, t)
            },
            p = function(t) {
                var e = t || new n.Field;
                e.FT = "/Ch";
                var r = e.Ff || 0;
                e.combo && (r = n.internal.setBitPosition(r, 18), delete e.combo), e.edit && (r = n.internal.setBitPosition(r, 19), delete e.edit), e.sort && (r = n.internal.setBitPosition(r, 20), delete e.sort), e.multiSelect && this.internal.getPDFVersion() >= 1.4 && (r = n.internal.setBitPosition(r, 22), delete e.multiSelect), e.doNotSpellCheck && this.internal.getPDFVersion() >= 1.4 && (r = n.internal.setBitPosition(r, 23), delete e.doNotSpellCheck), e.Ff = r, s.call(this, e)
            }
    })(e.API);
    var n = window.AcroForm;
    n.internal = {}, n.createFormXObject = function(t) {
            var e = new n.FormXObject,
                r = n.Appearance.internal.getHeight(t) || 0,
                i = n.Appearance.internal.getWidth(t) || 0;
            return e.BBox = [0, 0, i, r], e
        }, n.Appearance = {
            CheckBox: {
                createAppearanceStream: function() {
                    return {
                        N: {
                            On: n.Appearance.CheckBox.YesNormal
                        },
                        D: {
                            On: n.Appearance.CheckBox.YesPushDown,
                            Off: n.Appearance.CheckBox.OffPushDown
                        }
                    }
                },
                createMK: function() {
                    return "<< /CA (3)>>"
                },
                YesPushDown: function(t) {
                    var e = n.createFormXObject(t),
                        r = "";
                    t.Q = 1;
                    var i = n.internal.calculateX(t, "3", "ZapfDingbats", 50);
                    return r += "0.749023 g\n             0 0 " + n.Appearance.internal.getWidth(t) + " " + n.Appearance.internal.getHeight(t) + " re\n             f\n             BMC\n             q\n             0 0 1 rg\n             /F13 " + i.fontSize + " Tf 0 g\n             BT\n", r += i.text, r += "ET\n             Q\n             EMC\n", e.stream = r, e
                },
                YesNormal: function(t) {
                    var e = n.createFormXObject(t),
                        r = "";
                    t.Q = 1;
                    var i = n.internal.calculateX(t, "3", "ZapfDingbats", .9 * n.Appearance.internal.getHeight(t));
                    return r += "1 g\n0 0 " + n.Appearance.internal.getWidth(t) + " " + n.Appearance.internal.getHeight(t) + " re\nf\nq\n0 0 1 rg\n0 0 " + (n.Appearance.internal.getWidth(t) - 1) + " " + (n.Appearance.internal.getHeight(t) - 1) + " re\nW\nn\n0 g\nBT\n/F13 " + i.fontSize + " Tf 0 g\n", r += i.text, r += "ET\n             Q\n", e.stream = r, e
                },
                OffPushDown: function(t) {
                    var e = n.createFormXObject(t),
                        r = "";
                    return r += "0.749023 g\n            0 0 " + n.Appearance.internal.getWidth(t) + " " + n.Appearance.internal.getHeight(t) + " re\n            f\n", e.stream = r, e
                }
            },
            RadioButton: {
                Circle: {
                    createAppearanceStream: function(t) {
                        var e = {
                            D: {
                                Off: n.Appearance.RadioButton.Circle.OffPushDown
                            },
                            N: {}
                        };
                        return e.N[t] = n.Appearance.RadioButton.Circle.YesNormal, e.D[t] = n.Appearance.RadioButton.Circle.YesPushDown, e
                    },
                    createMK: function() {
                        return "<< /CA (l)>>"
                    },
                    YesNormal: function(t) {
                        var e = n.createFormXObject(t),
                            r = "",
                            i = n.Appearance.internal.getWidth(t) <= n.Appearance.internal.getHeight(t) ? n.Appearance.internal.getWidth(t) / 4 : n.Appearance.internal.getHeight(t) / 4;
                        i *= .9;
                        var o = n.Appearance.internal.Bezier_C;
                        return r += "q\n1 0 0 1 " + n.Appearance.internal.getWidth(t) / 2 + " " + n.Appearance.internal.getHeight(t) / 2 + " cm\n" + i + " 0 m\n" + i + " " + i * o + " " + i * o + " " + i + " 0 " + i + " c\n-" + i * o + " " + i + " -" + i + " " + i * o + " -" + i + " 0 c\n-" + i + " -" + i * o + " -" + i * o + " -" + i + " 0 -" + i + " c\n" + i * o + " -" + i + " " + i + " -" + i * o + " " + i + " 0 c\nf\nQ\n", e.stream = r, e
                    },
                    YesPushDown: function(t) {
                        var e = n.createFormXObject(t),
                            r = "",
                            i = n.Appearance.internal.getWidth(t) <= n.Appearance.internal.getHeight(t) ? n.Appearance.internal.getWidth(t) / 4 : n.Appearance.internal.getHeight(t) / 4,
                            o = 2 * (i *= .9),
                            a = o * n.Appearance.internal.Bezier_C,
                            s = i * n.Appearance.internal.Bezier_C;
                        return r += "0.749023 g\n            q\n           1 0 0 1 " + n.Appearance.internal.getWidth(t) / 2 + " " + n.Appearance.internal.getHeight(t) / 2 + " cm\n" + o + " 0 m\n" + o + " " + a + " " + a + " " + o + " 0 " + o + " c\n-" + a + " " + o + " -" + o + " " + a + " -" + o + " 0 c\n-" + o + " -" + a + " -" + a + " -" + o + " 0 -" + o + " c\n" + a + " -" + o + " " + o + " -" + a + " " + o + " 0 c\n            f\n            Q\n            0 g\n            q\n            1 0 0 1 " + n.Appearance.internal.getWidth(t) / 2 + " " + n.Appearance.internal.getHeight(t) / 2 + " cm\n" + i + " 0 m\n" + i + " " + s + " " + s + " " + i + " 0 " + i + " c\n-" + s + " " + i + " -" + i + " " + s + " -" + i + " 0 c\n-" + i + " -" + s + " -" + s + " -" + i + " 0 -" + i + " c\n" + s + " -" + i + " " + i + " -" + s + " " + i + " 0 c\n            f\n            Q\n", e.stream = r, e
                    },
                    OffPushDown: function(t) {
                        var e = n.createFormXObject(t),
                            r = "",
                            i = n.Appearance.internal.getWidth(t) <= n.Appearance.internal.getHeight(t) ? n.Appearance.internal.getWidth(t) / 4 : n.Appearance.internal.getHeight(t) / 4,
                            o = 2 * (i *= .9),
                            a = o * n.Appearance.internal.Bezier_C;
                        return r += "0.749023 g\n            q\n 1 0 0 1 " + n.Appearance.internal.getWidth(t) / 2 + " " + n.Appearance.internal.getHeight(t) / 2 + " cm\n" + o + " 0 m\n" + o + " " + a + " " + a + " " + o + " 0 " + o + " c\n-" + a + " " + o + " -" + o + " " + a + " -" + o + " 0 c\n-" + o + " -" + a + " -" + a + " -" + o + " 0 -" + o + " c\n" + a + " -" + o + " " + o + " -" + a + " " + o + " 0 c\n            f\n            Q\n", e.stream = r, e
                    }
                },
                Cross: {
                    createAppearanceStream: function(t) {
                        var e = {
                            D: {
                                Off: n.Appearance.RadioButton.Cross.OffPushDown
                            },
                            N: {}
                        };
                        return e.N[t] = n.Appearance.RadioButton.Cross.YesNormal, e.D[t] = n.Appearance.RadioButton.Cross.YesPushDown, e
                    },
                    createMK: function() {
                        return "<< /CA (8)>>"
                    },
                    YesNormal: function(t) {
                        var e = n.createFormXObject(t),
                            r = "",
                            i = n.Appearance.internal.calculateCross(t);
                        return r += "q\n            1 1 " + (n.Appearance.internal.getWidth(t) - 2) + " " + (n.Appearance.internal.getHeight(t) - 2) + " re\n            W\n            n\n            " + i.x1.x + " " + i.x1.y + " m\n            " + i.x2.x + " " + i.x2.y + " l\n            " + i.x4.x + " " + i.x4.y + " m\n            " + i.x3.x + " " + i.x3.y + " l\n            s\n            Q\n", e.stream = r, e
                    },
                    YesPushDown: function(t) {
                        var e = n.createFormXObject(t),
                            r = n.Appearance.internal.calculateCross(t),
                            i = "";
                        return i += "0.749023 g\n            0 0 " + n.Appearance.internal.getWidth(t) + " " + n.Appearance.internal.getHeight(t) + " re\n            f\n            q\n            1 1 " + (n.Appearance.internal.getWidth(t) - 2) + " " + (n.Appearance.internal.getHeight(t) - 2) + " re\n            W\n            n\n            " + r.x1.x + " " + r.x1.y + " m\n            " + r.x2.x + " " + r.x2.y + " l\n            " + r.x4.x + " " + r.x4.y + " m\n            " + r.x3.x + " " + r.x3.y + " l\n            s\n            Q\n", e.stream = i, e
                    },
                    OffPushDown: function(t) {
                        var e = n.createFormXObject(t),
                            r = "";
                        return r += "0.749023 g\n            0 0 " + n.Appearance.internal.getWidth(t) + " " + n.Appearance.internal.getHeight(t) + " re\n            f\n", e.stream = r, e
                    }
                }
            },
            createDefaultAppearanceStream: function(t) {
                var e = "";
                return e += "/Helv 0 Tf 0 g"
            }
        }, n.Appearance.internal = {
            Bezier_C: .551915024494,
            calculateCross: function(t) {
                var e = n.Appearance.internal.getWidth(t),
                    r = n.Appearance.internal.getHeight(t),
                    i = function(t, e) {
                        return t > e ? e : t
                    }(e, r);
                return {
                    x1: {
                        x: (e - i) / 2,
                        y: (r - i) / 2 + i
                    },
                    x2: {
                        x: (e - i) / 2 + i,
                        y: (r - i) / 2
                    },
                    x3: {
                        x: (e - i) / 2,
                        y: (r - i) / 2
                    },
                    x4: {
                        x: (e - i) / 2 + i,
                        y: (r - i) / 2 + i
                    }
                }
            }
        }, n.Appearance.internal.getWidth = function(t) {
            return t.Rect[2]
        }, n.Appearance.internal.getHeight = function(t) {
            return t.Rect[3]
        }, n.internal.inherit = function(t, e) {
            t.prototype = Object.create(e.prototype), t.prototype.constructor = t
        }, n.internal.arrayToPdfArray = function(t) {
            if (Array.isArray(t)) {
                var e = " [";
                for (var n in t) e += t[n].toString(), e += n < t.length - 1 ? " " : "";
                return e += "]"
            }
        }, n.internal.toPdfString = function(t) {
            return 0 !== (t = t || "").indexOf("(") && (t = "(" + t), ")" != t.substring(t.length - 1) && (t += "("), t
        }, n.PDFObject = function() {
            var t;
            Object.defineProperty(this, "objId", {
                get: function() {
                    return t || (this.internal ? t = this.internal.newObjectDeferred() : e.API.acroformPlugin.internal && (t = e.API.acroformPlugin.internal.newObjectDeferred())), t || console.log("Couldn't create Object ID"), t
                },
                configurable: !1
            })
        }, n.PDFObject.prototype.toString = function() {
            return this.objId + " 0 R"
        }, n.PDFObject.prototype.getString = function() {
            var t = this.objId + " 0 obj\n<<";
            return t += this.getContent() + ">>\n", this.stream && (t += "stream\n", t += this.stream, t += "endstream\n"), t += "endobj\n"
        }, n.PDFObject.prototype.getContent = function() {
            var t = "";
            return t += function(t) {
                var e = "",
                    r = Object.keys(t).filter(function(t) {
                        return "content" != t && "appearanceStreamContent" != t && "_" != t.substring(0, 1)
                    });
                for (var i in r) {
                    var o = r[i],
                        a = t[o];
                    a && (Array.isArray(a) ? e += "/" + o + " " + n.internal.arrayToPdfArray(a) + "\n" : a instanceof n.PDFObject ? e += "/" + o + " " + a.objId + " 0 R\n" : e += "/" + o + " " + a + "\n")
                }
                return e
            }(this)
        }, n.FormXObject = function() {
            n.PDFObject.call(this), this.Type = "/XObject", this.Subtype = "/Form", this.FormType = 1, this.BBox, this.Matrix, this.Resources = "2 0 R", this.PieceInfo;
            var t;
            Object.defineProperty(this, "Length", {
                enumerable: !0,
                get: function() {
                    return void 0 !== t ? t.length : 0
                }
            }), Object.defineProperty(this, "stream", {
                enumerable: !1,
                set: function(e) {
                    t = e
                },
                get: function() {
                    return t || null
                }
            })
        }, n.internal.inherit(n.FormXObject, n.PDFObject), n.AcroFormDictionary = function() {
            n.PDFObject.call(this);
            var t = [];
            Object.defineProperty(this, "Kids", {
                enumerable: !1,
                configurable: !0,
                get: function() {
                    return t.length > 0 ? t : void 0
                }
            }), Object.defineProperty(this, "Fields", {
                enumerable: !0,
                configurable: !0,
                get: function() {
                    return t
                }
            }), this.DA
        }, n.internal.inherit(n.AcroFormDictionary, n.PDFObject), n.Field = function() {
            n.PDFObject.call(this);
            var t;
            Object.defineProperty(this, "Rect", {
                enumerable: !0,
                configurable: !1,
                get: function() {
                    if (t) return t
                },
                set: function(e) {
                    t = e
                }
            });
            var e = "";
            Object.defineProperty(this, "FT", {
                enumerable: !0,
                set: function(t) {
                    e = t
                },
                get: function() {
                    return e
                }
            });
            var r;
            Object.defineProperty(this, "T", {
                enumerable: !0,
                configurable: !1,
                set: function(t) {
                    r = t
                },
                get: function() {
                    if (!r || r.length < 1) {
                        if (this instanceof n.ChildClass) return;
                        return "(FieldObject" + n.Field.FieldNum++ + ")"
                    }
                    return "(" == r.substring(0, 1) && r.substring(r.length - 1) ? r : "(" + r + ")"
                }
            });
            var i;
            Object.defineProperty(this, "DA", {
                enumerable: !0,
                get: function() {
                    if (i) return "(" + i + ")"
                },
                set: function(t) {
                    i = t
                }
            });
            var o;
            Object.defineProperty(this, "DV", {
                enumerable: !0,
                configurable: !0,
                get: function() {
                    if (o) return o
                },
                set: function(t) {
                    o = t
                }
            }), Object.defineProperty(this, "Type", {
                enumerable: !0,
                get: function() {
                    return this.hasAnnotation ? "/Annot" : null
                }
            }), Object.defineProperty(this, "Subtype", {
                enumerable: !0,
                get: function() {
                    return this.hasAnnotation ? "/Widget" : null
                }
            }), this.BG, Object.defineProperty(this, "hasAnnotation", {
                enumerable: !1,
                get: function() {
                    return !!(this.Rect || this.BC || this.BG)
                }
            }), Object.defineProperty(this, "hasAppearanceStream", {
                enumerable: !1,
                configurable: !0,
                writable: !0
            }), Object.defineProperty(this, "page", {
                enumerable: !1,
                configurable: !0,
                writable: !0
            })
        }, n.Field.FieldNum = 0, n.internal.inherit(n.Field, n.PDFObject), n.ChoiceField = function() {
            n.Field.call(this), this.FT = "/Ch", this.Opt = [], this.V = "()", this.TI = 0, this.combo = !1, Object.defineProperty(this, "edit", {
                enumerable: !0,
                set: function(t) {
                    1 == t ? (this._edit = !0, this.combo = !0) : this._edit = !1
                },
                get: function() {
                    return !!this._edit && this._edit
                },
                configurable: !1
            }), this.hasAppearanceStream = !0, Object.defineProperty(this, "V", {
                get: function() {
                    n.internal.toPdfString()
                }
            })
        }, n.internal.inherit(n.ChoiceField, n.Field), window.ChoiceField = n.ChoiceField, n.ListBox = function() {
            n.ChoiceField.call(this)
        }, n.internal.inherit(n.ListBox, n.ChoiceField), window.ListBox = n.ListBox, n.ComboBox = function() {
            n.ListBox.call(this), this.combo = !0
        }, n.internal.inherit(n.ComboBox, n.ListBox), window.ComboBox = n.ComboBox, n.EditBox = function() {
            n.ComboBox.call(this), this.edit = !0
        }, n.internal.inherit(n.EditBox, n.ComboBox), window.EditBox = n.EditBox, n.Button = function() {
            n.Field.call(this), this.FT = "/Btn"
        }, n.internal.inherit(n.Button, n.Field), window.Button = n.Button, n.PushButton = function() {
            n.Button.call(this), this.pushbutton = !0
        }, n.internal.inherit(n.PushButton, n.Button), window.PushButton = n.PushButton, n.RadioButton = function() {
            n.Button.call(this), this.radio = !0;
            var t = [];
            Object.defineProperty(this, "Kids", {
                enumerable: !0,
                get: function() {
                    if (t.length > 0) return t
                }
            }), Object.defineProperty(this, "__Kids", {
                get: function() {
                    return t
                }
            });
            var e;
            Object.defineProperty(this, "noToggleToOff", {
                enumerable: !1,
                get: function() {
                    return e
                },
                set: function(t) {
                    e = t
                }
            })
        }, n.internal.inherit(n.RadioButton, n.Button), window.RadioButton = n.RadioButton, n.ChildClass = function(t, e) {
            n.Field.call(this), this.Parent = t, this._AppearanceType = n.Appearance.RadioButton.Circle, this.appearanceStreamContent = this._AppearanceType.createAppearanceStream(e), this.F = n.internal.setBitPosition(this.F, 3, 1), this.MK = this._AppearanceType.createMK(), this.AS = "/Off", this._Name = e
        }, n.internal.inherit(n.ChildClass, n.Field), n.RadioButton.prototype.setAppearance = function(t) {
            if ("createAppearanceStream" in t && "createMK" in t)
                for (var e in this.__Kids) {
                    var n = this.__Kids[e];
                    n.appearanceStreamContent = t.createAppearanceStream(n._Name), n.MK = t.createMK()
                } else console.log("Couldn't assign Appearance to RadioButton. Appearance was Invalid!")
        }, n.RadioButton.prototype.createOption = function(t) {
            var r = this,
                i = new n.ChildClass(r, t);
            return this.__Kids.push(i), e.API.addField(i), i
        }, n.CheckBox = function() {
            Button.call(this), this.appearanceStreamContent = n.Appearance.CheckBox.createAppearanceStream(), this.MK = n.Appearance.CheckBox.createMK(), this.AS = "/On", this.V = "/On"
        }, n.internal.inherit(n.CheckBox, n.Button), window.CheckBox = n.CheckBox, n.TextField = function() {
            n.Field.call(this), this.DA = n.Appearance.createDefaultAppearanceStream(), this.F = 4;
            var t;
            Object.defineProperty(this, "V", {
                get: function() {
                    return t ? "(" + t + ")" : t
                },
                enumerable: !0,
                set: function(e) {
                    t = e
                }
            });
            var e;
            Object.defineProperty(this, "DV", {
                get: function() {
                    return e ? "(" + e + ")" : e
                },
                enumerable: !0,
                set: function(t) {
                    e = t
                }
            });
            var r = !1;
            Object.defineProperty(this, "multiline", {
                enumerable: !1,
                get: function() {
                    return r
                },
                set: function(t) {
                    r = t
                }
            });
            var i = !1;
            Object.defineProperty(this, "MaxLen", {
                enumerable: !0,
                get: function() {
                    return i
                },
                set: function(t) {
                    i = t
                }
            }), Object.defineProperty(this, "hasAppearanceStream", {
                enumerable: !1,
                get: function() {
                    return this.V || this.DV
                }
            })
        }, n.internal.inherit(n.TextField, n.Field), window.TextField = n.TextField, n.PasswordField = function() {
            TextField.call(this), Object.defineProperty(this, "password", {
                value: !0,
                enumerable: !1,
                configurable: !1,
                writable: !1
            })
        }, n.internal.inherit(n.PasswordField, n.TextField), window.PasswordField = n.PasswordField, n.internal.calculateFontSpace = function(t, e, r) {
            var r = r || "helvetica",
                i = n.internal.calculateFontSpace.canvas || (n.internal.calculateFontSpace.canvas = document.createElement("canvas"));
            (s = i.getContext("2d")).save();
            var o = e + " " + r;
            s.font = o;
            var a = s.measureText(t);
            s.fontcolor = "black";
            var s = i.getContext("2d");
            return a.height = 1.5 * s.measureText("3").width, s.restore(), a
        }, n.internal.calculateX = function(t, e, r, i) {
            var i = i || 12,
                r = r || "helvetica",
                o = {
                    text: "",
                    fontSize: ""
                },
                a = (e = ")" == (e = "(" == e.substr(0, 1) ? e.substr(1) : e).substr(e.length - 1) ? e.substr(0, e.length - 1) : e).split(" "),
                s = i,
                c = n.Appearance.internal.getHeight(t) || 0;
            c = c < 0 ? -c : c;
            var l = n.Appearance.internal.getWidth(t) || 0;
            l = l < 0 ? -l : l;
            s++;
            t: for (;;) {
                var e = "";
                s--;
                var u = n.internal.calculateFontSpace("3", s + "px", r).height,
                    h = t.multiline ? c - s : (c - u) / 2,
                    f = -2,
                    d = h += 2,
                    p = 0,
                    m = 0,
                    g = 0;
                if (0 == s) {
                    s = 12, e = "(...) Tj\n", e += "% Width of Text: " + n.internal.calculateFontSpace(e, "1px").width + ", FieldWidth:" + l + "\n";
                    break
                }
                g = n.internal.calculateFontSpace(a[0] + " ", s + "px", r).width;
                var w = "",
                    y = 0;
                for (var v in a) {
                    w = " " == (w += a[v] + " ").substr(w.length - 1) ? w.substr(0, w.length - 1) : w;
                    var b = parseInt(v);
                    g = n.internal.calculateFontSpace(w + " ", s + "px", r).width;
                    var x = function(t, e, i) {
                            if (t + 1 < a.length) {
                                var o = e + " " + a[t + 1];
                                return n.internal.calculateFontSpace(o, i + "px", r).width <= l - 4
                            }
                            return !1
                        }(b, w, s),
                        k = v >= a.length - 1;
                    if (!x || k) {
                        if (x || k) {
                            if (k) m = b;
                            else if (t.multiline && (u + 2) * (y + 2) + 2 > c) continue t
                        } else {
                            if (!t.multiline) continue t;
                            if ((u + 2) * (y + 2) + 2 > c) continue t;
                            m = b
                        }
                        for (var _ = "", S = p; S <= m; S++) _ += a[S] + " ";
                        switch (_ = " " == _.substr(_.length - 1) ? _.substr(0, _.length - 1) : _, g = n.internal.calculateFontSpace(_, s + "px", r).width, t.Q) {
                            case 2:
                                f = l - g - 2;
                                break;
                            case 1:
                                f = (l - g) / 2;
                                break;
                            case 0:
                            default:
                                f = 2
                        }
                        e += f + " " + d + " Td\n", e += "(" + _ + ") Tj\n", e += -f + " 0 Td\n", d = -(s + 2), g = 0, p = m + 1, y++, w = ""
                    } else w += " "
                }
                break
            }
            return o.text = e, o.fontSize = s, o
        }, n.internal.calculateAppearanceStream = function(t) {
            if (t.appearanceStreamContent) return t.appearanceStreamContent;
            if (t.V || t.DV) {
                var e = "",
                    r = t.V || t.DV,
                    i = n.internal.calculateX(t, r);
                e += "/Tx BMC\nq\n/F1 " + i.fontSize + " Tf\n1 0 0 1 0 0 Tm\n", e += "BT\n", e += i.text, e += "ET\n", e += "Q\nEMC\n";
                var o = new n.createFormXObject(t);
                return o.stream = e, o
            }
        }, n.internal.calculateCoordinates = function(t, e, r, i) {
            var o = {};
            if (this.internal) {
                var a = function(t) {
                    return t * this.internal.scaleFactor
                };
                Array.isArray(t) ? (t[0] = n.scale(t[0]), t[1] = n.scale(t[1]), t[2] = n.scale(t[2]), t[3] = n.scale(t[3]), o.lowerLeft_X = t[0] || 0, o.lowerLeft_Y = a.call(this, this.internal.pageSize.height) - t[3] - t[1] || 0, o.upperRight_X = t[0] + t[2] || 0, o.upperRight_Y = a.call(this, this.internal.pageSize.height) - t[1] || 0) : (t = n.scale(t), e = n.scale(e), r = n.scale(r), i = n.scale(i), o.lowerLeft_X = t || 0, o.lowerLeft_Y = this.internal.pageSize.height - e || 0, o.upperRight_X = t + r || 0, o.upperRight_Y = this.internal.pageSize.height - e + i || 0)
            } else Array.isArray(t) ? (o.lowerLeft_X = t[0] || 0, o.lowerLeft_Y = t[1] || 0, o.upperRight_X = t[0] + t[2] || 0, o.upperRight_Y = t[1] + t[3] || 0) : (o.lowerLeft_X = t || 0, o.lowerLeft_Y = e || 0, o.upperRight_X = t + r || 0, o.upperRight_Y = e + i || 0);
            return [o.lowerLeft_X, o.lowerLeft_Y, o.upperRight_X, o.upperRight_Y]
        }, n.internal.calculateColor = function(t, e, n) {
            var r = new Array(3);
            return r.r = 0 | t, r.g = 0 | e, r.b = 0 | n, r
        }, n.internal.getBitPosition = function(t, e) {
            var n = 1;
            return n <<= e - 1, (t = t || 0) | n
        }, n.internal.setBitPosition = function(t, e, n) {
            t = t || 0;
            var r = 1;
            if (r <<= e - 1, 1 == (n = n || 1)) t = t | r;
            else var t = t & ~r;
            return t
        }, e.API.addHTML = function(t, e, n, r, i) {
            if ("undefined" == typeof html2canvas && "undefined" == typeof rasterizeHTML) throw new Error("You need either https://github.com/niklasvh/html2canvas or https://github.com/cburgmer/rasterizeHTML.js");
            "number" != typeof e && (r = e, i = n), "function" == typeof r && (i = r, r = null);
            var o = this.internal,
                a = o.scaleFactor,
                s = o.pageSize.width,
                c = o.pageSize.height;
            if (r = r || {}, r.onrendered = function(t) {
                    e = parseInt(e) || 0, n = parseInt(n) || 0;
                    var o = r.dim || {},
                        l = o.h || 0,
                        u = o.w || Math.min(s, t.width / a) - e,
                        h = "JPEG";
                    if (r.format && (h = r.format), t.height > c && r.pagesplit) {
                        var f = function() {
                            for (var r = 0;;) {
                                var o = document.createElement("canvas");
                                o.width = Math.min(s * a, t.width), o.height = Math.min(c * a, t.height - r), o.getContext("2d").drawImage(t, 0, r, t.width, o.height, 0, 0, o.width, o.height);
                                var l = [o, e, r ? 0 : n, o.width / a, o.height / a, h, null, "SLOW"];
                                if (this.addImage.apply(this, l), (r += o.height) >= t.height) break;
                                this.addPage()
                            }
                            i(u, r, null, l)
                        }.bind(this);
                        if ("CANVAS" === t.nodeName) {
                            var d = new Image;
                            d.onload = f, d.src = t.toDataURL("image/png"), t = d
                        } else f()
                    } else {
                        var p = Math.random().toString(35),
                            m = [t, e, n, u, l, h, p, "SLOW"];
                        this.addImage.apply(this, m), i(u, l, p, m)
                    }
                }.bind(this), "undefined" != typeof html2canvas && !r.rstz) return html2canvas(t, r);
            if ("undefined" != typeof rasterizeHTML) {
                var l = "drawDocument";
                return "string" == typeof t && (l = /^http/.test(t) ? "drawURL" : "drawHTML"), r.width = r.width || s * a, rasterizeHTML[l](t, void 0, r).then(function(t) {
                    r.onrendered(t.image)
                }, function(t) {
                    i(null, t)
                })
            }
            return null
        },
        /** @preserve
         * jsPDF addImage plugin
         * Copyright (c) 2012 Jason Siefken, https://github.com/siefkenj/
         *               2013 Chris Dowling, https://github.com/gingerchris
         *               2013 Trinh Ho, https://github.com/ineedfat
         *               2013 Edwin Alejandro Perez, https://github.com/eaparango
         *               2013 Norah Smith, https://github.com/burnburnrocket
         *               2014 Diego Casorran, https://github.com/diegocr
         *               2014 James Robb, https://github.com/jamesbrobb
         *
         * 
         */
        function(e) {
            var n = ["jpeg", "jpg", "png"],
                r = function t(e) {
                    var n = this.internal.newObject(),
                        r = this.internal.write,
                        i = this.internal.putStream;
                    if (e.n = n, r("<</Type /XObject"), r("/Subtype /Image"), r("/Width " + e.w), r("/Height " + e.h), e.cs === this.color_spaces.INDEXED ? r("/ColorSpace [/Indexed /DeviceRGB " + (e.pal.length / 3 - 1) + " " + ("smask" in e ? n + 2 : n + 1) + " 0 R]") : (r("/ColorSpace /" + e.cs), e.cs === this.color_spaces.DEVICE_CMYK && r("/Decode [1 0 1 0 1 0 1 0]")), r("/BitsPerComponent " + e.bpc), "f" in e && r("/Filter /" + e.f), "dp" in e && r("/DecodeParms <<" + e.dp + ">>"), "trns" in e && e.trns.constructor == Array) {
                        for (var o = "", a = 0, s = e.trns.length; a < s; a++) o += e.trns[a] + " " + e.trns[a] + " ";
                        r("/Mask [" + o + "]")
                    }
                    if ("smask" in e && r("/SMask " + (n + 1) + " 0 R"), r("/Length " + e.data.length + ">>"), i(e.data), r("endobj"), "smask" in e) {
                        var c = "/Predictor " + e.p + " /Colors 1 /BitsPerComponent " + e.bpc + " /Columns " + e.w,
                            l = {
                                w: e.w,
                                h: e.h,
                                cs: "DeviceGray",
                                bpc: e.bpc,
                                dp: c,
                                data: e.smask
                            };
                        "f" in e && (l.f = e.f), t.call(this, l)
                    }
                    e.cs === this.color_spaces.INDEXED && (this.internal.newObject(), r("<< /Length " + e.pal.length + ">>"), i(this.arrayBufferToBinaryString(new Uint8Array(e.pal))), r("endobj"))
                },
                i = function() {
                    var t = this.internal.collections.addImage_images;
                    for (var e in t) r.call(this, t[e])
                },
                o = function() {
                    var t, e = this.internal.collections.addImage_images,
                        n = this.internal.write;
                    for (var r in e) n("/I" + (t = e[r]).i, t.n, "0", "R")
                },
                a = function(t) {
                    return t && "string" == typeof t && (t = t.toUpperCase()), t in e.image_compression ? t : e.image_compression.NONE
                },
                s = function() {
                    var t = this.internal.collections.addImage_images;
                    return t || (this.internal.collections.addImage_images = t = {}, this.internal.events.subscribe("putResources", i), this.internal.events.subscribe("putXobjectDict", o)), t
                },
                c = function(t) {
                    var e = 0;
                    return t && (e = Object.keys ? Object.keys(t).length : function(t) {
                        var e = 0;
                        for (var n in t) t.hasOwnProperty(n) && e++;
                        return e
                    }(t)), e
                },
                l = function(t) {
                    return void 0 === t || null === t
                },
                u = function(t) {
                    return "string" == typeof t && e.sHashCode(t)
                },
                h = function(t) {
                    return -1 === n.indexOf(t)
                },
                f = function(t) {
                    return "function" != typeof e["process" + t.toUpperCase()]
                },
                d = function(e) {
                    return "object" === (void 0 === e ? "undefined" : t(e)) && 1 === e.nodeType
                },
                p = function(e, n, r) {
                    if ("IMG" === e.nodeName && e.hasAttribute("src")) {
                        var i = "" + e.getAttribute("src");
                        if (!r && 0 === i.indexOf("data:image/")) return i;
                        !n && /\.png(?:[?#].*)?$/i.test(i) && (n = "png")
                    }
                    if ("CANVAS" === e.nodeName) var o = e;
                    else {
                        (o = document.createElement("canvas")).width = e.clientWidth || e.width, o.height = e.clientHeight || e.height;
                        var a = o.getContext("2d");
                        if (!a) throw "addImage requires canvas to be supported by browser.";
                        if (r) {
                            var s, c, l, u, h, f, d, p, m = Math.PI / 180;
                            "object" === (void 0 === r ? "undefined" : t(r)) && (s = r.x, c = r.y, l = r.bg, r = r.angle), p = r * m, u = Math.abs(Math.cos(p)), h = Math.abs(Math.sin(p)), f = o.width, d = o.height, o.width = d * h + f * u, o.height = d * u + f * h, isNaN(s) && (s = o.width / 2), isNaN(c) && (c = o.height / 2), a.clearRect(0, 0, o.width, o.height), a.fillStyle = l || "white", a.fillRect(0, 0, o.width, o.height), a.save(), a.translate(s, c), a.rotate(p), a.drawImage(e, -f / 2, -d / 2), a.rotate(-p), a.translate(-s, -c), a.restore()
                        } else a.drawImage(e, 0, 0, o.width, o.height)
                    }
                    return o.toDataURL("png" == ("" + n).toLowerCase() ? "image/png" : "image/jpeg")
                },
                m = function(t, e) {
                    var n;
                    if (e)
                        for (var r in e)
                            if (t === e[r].alias) {
                                n = e[r];
                                break
                            }
                    return n
                },
                g = function(t, e, n) {
                    return t || e || (t = -96, e = -96), t < 0 && (t = -1 * n.w * 72 / t / this.internal.scaleFactor), e < 0 && (e = -1 * n.h * 72 / e / this.internal.scaleFactor), 0 === t && (t = e * n.w / n.h), 0 === e && (e = t * n.h / n.w), [t, e]
                },
                w = function(t, e, n, r, i, o, a) {
                    var s = g.call(this, n, r, i),
                        c = this.internal.getCoordinateString,
                        l = this.internal.getVerticalCoordinateString;
                    n = s[0], r = s[1], a[o] = i, this.internal.write("q", c(n), "0 0", c(r), c(t), l(e + r), "cm /I" + i.i, "Do Q")
                };
            e.color_spaces = {
                DEVICE_RGB: "DeviceRGB",
                DEVICE_GRAY: "DeviceGray",
                DEVICE_CMYK: "DeviceCMYK",
                CAL_GREY: "CalGray",
                CAL_RGB: "CalRGB",
                LAB: "Lab",
                ICC_BASED: "ICCBased",
                INDEXED: "Indexed",
                PATTERN: "Pattern",
                SEPARATION: "Separation",
                DEVICE_N: "DeviceN"
            }, e.decode = {
                DCT_DECODE: "DCTDecode",
                FLATE_DECODE: "FlateDecode",
                LZW_DECODE: "LZWDecode",
                JPX_DECODE: "JPXDecode",
                JBIG2_DECODE: "JBIG2Decode",
                ASCII85_DECODE: "ASCII85Decode",
                ASCII_HEX_DECODE: "ASCIIHexDecode",
                RUN_LENGTH_DECODE: "RunLengthDecode",
                CCITT_FAX_DECODE: "CCITTFaxDecode"
            }, e.image_compression = {
                NONE: "NONE",
                FAST: "FAST",
                MEDIUM: "MEDIUM",
                SLOW: "SLOW"
            }, e.sHashCode = function(t) {
                return Array.prototype.reduce && t.split("").reduce(function(t, e) {
                    return (t = (t << 5) - t + e.charCodeAt(0)) & t
                }, 0)
            }, e.isString = function(t) {
                return "string" == typeof t
            }, e.extractInfoFromBase64DataURI = function(t) {
                return /^data:([\w]+?\/([\w]+?));base64,(.+?)$/g.exec(t)
            }, e.supportsArrayBuffer = function() {
                return "undefined" != typeof ArrayBuffer && "undefined" != typeof Uint8Array
            }, e.isArrayBuffer = function(t) {
                return !!this.supportsArrayBuffer() && t instanceof ArrayBuffer
            }, e.isArrayBufferView = function(t) {
                return !!this.supportsArrayBuffer() && ("undefined" != typeof Uint32Array && (t instanceof Int8Array || t instanceof Uint8Array || "undefined" != typeof Uint8ClampedArray && t instanceof Uint8ClampedArray || t instanceof Int16Array || t instanceof Uint16Array || t instanceof Int32Array || t instanceof Uint32Array || t instanceof Float32Array || t instanceof Float64Array))
            }, e.binaryStringToUint8Array = function(t) {
                for (var e = t.length, n = new Uint8Array(e), r = 0; r < e; r++) n[r] = t.charCodeAt(r);
                return n
            }, e.arrayBufferToBinaryString = function(t) {
                if ("function" == typeof window.atob) return atob(this.arrayBufferToBase64(t));
                for (var e = this.isArrayBuffer(t) ? t : new Uint8Array(t), n = "", r = Math.round(e.byteLength / 20480), i = 0; i < r; i++) n += String.fromCharCode.apply(null, e.slice(20480 * i, 20480 * i + 20480));
                return n
            }, e.arrayBufferToBase64 = function(t) {
                for (var e, n = "", r = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/", i = new Uint8Array(t), o = i.byteLength, a = o % 3, s = o - a, c = 0; c < s; c += 3) n += r[(16515072 & (e = i[c] << 16 | i[c + 1] << 8 | i[c + 2])) >> 18] + r[(258048 & e) >> 12] + r[(4032 & e) >> 6] + r[63 & e];
                return 1 == a ? n += r[(252 & (e = i[s])) >> 2] + r[(3 & e) << 4] + "==" : 2 == a && (n += r[(64512 & (e = i[s] << 8 | i[s + 1])) >> 10] + r[(1008 & e) >> 4] + r[(15 & e) << 2] + "="), n
            }, e.createImageInfo = function(t, e, n, r, i, o, a, s, c, l, u, h, f) {
                var d = {
                    alias: s,
                    w: e,
                    h: n,
                    cs: r,
                    bpc: i,
                    i: a,
                    data: t
                };
                return o && (d.f = o), c && (d.dp = c), l && (d.trns = l), u && (d.pal = u), h && (d.smask = h), f && (d.p = f), d
            }, e.addImage = function(e, r, i, o, g, y, v, b, x) {
                if ("string" != typeof r) {
                    var k = y;
                    y = g, g = o, o = i, i = r, r = k
                }
                if ("object" === (void 0 === e ? "undefined" : t(e)) && !d(e) && "imageData" in e) {
                    var _ = e;
                    e = _.imageData, r = _.format || r, i = _.x || i || 0, o = _.y || o || 0, g = _.w || g, y = _.h || y, v = _.alias || v, b = _.compression || b, x = _.rotation || _.angle || x
                }
                if (isNaN(i) || isNaN(o)) throw console.error("jsPDF.addImage: Invalid coordinates", arguments), new Error("Invalid coordinates passed to jsPDF.addImage");
                var S, C = s.call(this);
                if (!(S = m(e, C))) {
                    var A;
                    if (d(e) && (e = p(e, r, x)), l(v) && (v = u(e)), !(S = m(v, C))) {
                        if (this.isString(e)) {
                            var q = this.extractInfoFromBase64DataURI(e);
                            q ? (r = q[2], e = atob(q[3])) : 137 === e.charCodeAt(0) && 80 === e.charCodeAt(1) && 78 === e.charCodeAt(2) && 71 === e.charCodeAt(3) && (r = "png")
                        }
                        if (r = (r || "JPEG").toLowerCase(), h(r)) throw new Error("addImage currently only supports formats " + n + ", not '" + r + "'");
                        if (f(r)) throw new Error("please ensure that the plugin for '" + r + "' support is added");
                        if (this.supportsArrayBuffer() && (e instanceof Uint8Array || (A = e, e = this.binaryStringToUint8Array(e))), !(S = this["process" + r.toUpperCase()](e, c(C), v, a(b), A))) throw new Error("An unkwown error occurred whilst processing the image")
                    }
                }
                return w.call(this, i, o, g, y, S, S.i, C), this
            };
            var y = function(t) {
                    var e, n, r;
                    if (255 === !t.charCodeAt(0) || 216 === !t.charCodeAt(1) || 255 === !t.charCodeAt(2) || 224 === !t.charCodeAt(3) || !t.charCodeAt(6) === "J".charCodeAt(0) || !t.charCodeAt(7) === "F".charCodeAt(0) || !t.charCodeAt(8) === "I".charCodeAt(0) || !t.charCodeAt(9) === "F".charCodeAt(0) || 0 === !t.charCodeAt(10)) throw new Error("getJpegSize requires a binary string jpeg file");
                    for (var i = 256 * t.charCodeAt(4) + t.charCodeAt(5), o = 4, a = t.length; o < a;) {
                        if (o += i, 255 !== t.charCodeAt(o)) throw new Error("getJpegSize could not find the size of the image");
                        if (192 === t.charCodeAt(o + 1) || 193 === t.charCodeAt(o + 1) || 194 === t.charCodeAt(o + 1) || 195 === t.charCodeAt(o + 1) || 196 === t.charCodeAt(o + 1) || 197 === t.charCodeAt(o + 1) || 198 === t.charCodeAt(o + 1) || 199 === t.charCodeAt(o + 1)) return n = 256 * t.charCodeAt(o + 5) + t.charCodeAt(o + 6), e = 256 * t.charCodeAt(o + 7) + t.charCodeAt(o + 8), r = t.charCodeAt(o + 9), [e, n, r];
                        o += 2, i = 256 * t.charCodeAt(o) + t.charCodeAt(o + 1)
                    }
                },
                v = function(t) {
                    if (65496 !== (t[0] << 8 | t[1])) throw new Error("Supplied data is not a JPEG");
                    for (var e, n, r, i, o = t.length, a = (t[4] << 8) + t[5], s = 4; s < o;) {
                        if (s += a, e = b(t, s), a = (e[2] << 8) + e[3], (192 === e[1] || 194 === e[1]) && 255 === e[0] && a > 7) return e = b(t, s + 5), n = (e[2] << 8) + e[3], r = (e[0] << 8) + e[1], i = e[4], {
                            width: n,
                            height: r,
                            numcomponents: i
                        };
                        s += 2
                    }
                    throw new Error("getJpegSizeFromBytes could not find the size of the image")
                },
                b = function(t, e) {
                    return t.subarray(e, e + 5)
                };
            e.processJPEG = function(t, e, n, r, i) {
                var o, a = this.color_spaces.DEVICE_RGB,
                    s = this.decode.DCT_DECODE;
                return this.isString(t) ? (o = y(t), this.createImageInfo(t, o[0], o[1], 1 == o[3] ? this.color_spaces.DEVICE_GRAY : a, 8, s, e, n)) : (this.isArrayBuffer(t) && (t = new Uint8Array(t)), this.isArrayBufferView(t) ? (o = v(t), t = i || this.arrayBufferToBinaryString(t), this.createImageInfo(t, o.width, o.height, 1 == o.numcomponents ? this.color_spaces.DEVICE_GRAY : a, 8, s, e, n)) : null)
            }, e.processJPG = function() {
                return this.processJPEG.apply(this, arguments)
            }
        }(e.API),
        /**
         * jsPDF Annotations PlugIn
         * Copyright (c) 2014 Steven Spungin (TwelveTone LLC)  steven@twelvetone.tv
         *
         * Licensed under the MIT License.
         * http://opensource.org/licenses/mit-license
         */
        function(t) {
            var n = {
                annotations: [],
                f2: function(t) {
                    return t.toFixed(2)
                },
                notEmpty: function(t) {
                    if (void 0 !== t && "" != t) return !0
                }
            };
            e.API.annotationPlugin = n, e.API.events.push(["addPage", function(t) {
                this.annotationPlugin.annotations[t.pageNumber] = []
            }]), t.events.push(["putPage", function(t) {
                for (var e = this.annotationPlugin.annotations[t.pageNumber], r = !1, i = 0; i < e.length && !r; i++) switch ((l = e[i]).type) {
                    case "link":
                        if (n.notEmpty(l.options.url) || n.notEmpty(l.options.pageNumber)) {
                            r = !0;
                            break
                        }
                    case "reference":
                    case "text":
                    case "freetext":
                        r = !0
                }
                if (0 != r) {
                    this.internal.write("/Annots [");
                    for (var o = this.annotationPlugin.f2, a = this.internal.scaleFactor, s = this.internal.pageSize.height, c = this.internal.getPageInfo(t.pageNumber), i = 0; i < e.length; i++) {
                        var l = e[i];
                        switch (l.type) {
                            case "reference":
                                this.internal.write(" " + l.object.objId + " 0 R ");
                                break;
                            case "text":
                                var u = this.internal.newAdditionalObject(),
                                    h = this.internal.newAdditionalObject(),
                                    f = l.title || "Note";
                                w = "<</Type /Annot /Subtype /Text " + (p = "/Rect [" + o(l.bounds.x * a) + " " + o(s - (l.bounds.y + l.bounds.h) * a) + " " + o((l.bounds.x + l.bounds.w) * a) + " " + o((s - l.bounds.y) * a) + "] ") + "/Contents (" + l.contents + ")", w += " /Popup " + h.objId + " 0 R", w += " /P " + c.objId + " 0 R", w += " /T (" + f + ") >>", u.content = w;
                                var d = u.objId + " 0 R";
                                w = "<</Type /Annot /Subtype /Popup " + (p = "/Rect [" + o((l.bounds.x + 30) * a) + " " + o(s - (l.bounds.y + l.bounds.h) * a) + " " + o((l.bounds.x + l.bounds.w + 30) * a) + " " + o((s - l.bounds.y) * a) + "] ") + " /Parent " + d, l.open && (w += " /Open true"), w += " >>", h.content = w, this.internal.write(u.objId, "0 R", h.objId, "0 R");
                                break;
                            case "freetext":
                                var p = "/Rect [" + o(l.bounds.x * a) + " " + o((s - l.bounds.y) * a) + " " + o(l.bounds.x + l.bounds.w * a) + " " + o(s - (l.bounds.y + l.bounds.h) * a) + "] ",
                                    m = l.color || "#000000";
                                w = "<</Type /Annot /Subtype /FreeText " + p + "/Contents (" + l.contents + ")", w += " /DS(font: Helvetica,sans-serif 12.0pt; text-align:left; color:#" + m + ")", w += " /Border [0 0 0]", w += " >>", this.internal.write(w);
                                break;
                            case "link":
                                if (l.options.name) {
                                    var g = this.annotations._nameMap[l.options.name];
                                    l.options.pageNumber = g.page, l.options.top = g.y
                                } else l.options.top || (l.options.top = 0);
                                var p = "/Rect [" + o(l.x * a) + " " + o((s - l.y) * a) + " " + o((l.x + l.w) * a) + " " + o((s - (l.y + l.h)) * a) + "] ",
                                    w = "";
                                if (l.options.url) w = "<</Type /Annot /Subtype /Link " + p + "/Border [0 0 0] /A <</S /URI /URI (" + l.options.url + ") >>";
                                else if (l.options.pageNumber) switch (w = "<</Type /Annot /Subtype /Link " + p + "/Border [0 0 0] /Dest [" + (t = this.internal.getPageInfo(l.options.pageNumber)).objId + " 0 R", l.options.magFactor = l.options.magFactor || "XYZ", l.options.magFactor) {
                                    case "Fit":
                                        w += " /Fit]";
                                        break;
                                    case "FitH":
                                        w += " /FitH " + l.options.top + "]";
                                        break;
                                    case "FitV":
                                        l.options.left = l.options.left || 0, w += " /FitV " + l.options.left + "]";
                                        break;
                                    case "XYZ":
                                    default:
                                        var y = o((s - l.options.top) * a);
                                        l.options.left = l.options.left || 0, void 0 === l.options.zoom && (l.options.zoom = 0), w += " /XYZ " + l.options.left + " " + y + " " + l.options.zoom + "]"
                                }
                                "" != w && (w += " >>", this.internal.write(w))
                        }
                    }
                    this.internal.write("]")
                }
            }]), t.createAnnotation = function(t) {
                switch (t.type) {
                    case "link":
                        this.link(t.bounds.x, t.bounds.y, t.bounds.w, t.bounds.h, t);
                        break;
                    case "text":
                    case "freetext":
                        this.annotationPlugin.annotations[this.internal.getCurrentPageInfo().pageNumber].push(t)
                }
            }, t.link = function(t, e, n, r, i) {
                this.annotationPlugin.annotations[this.internal.getCurrentPageInfo().pageNumber].push({
                    x: t,
                    y: e,
                    w: n,
                    h: r,
                    options: i,
                    type: "link"
                })
            }, t.textWithLink = function(t, e, n, r) {
                var i = this.getTextWidth(t),
                    o = this.internal.getLineHeight() / this.internal.scaleFactor;
                return this.text(t, e, n), n += .2 * o, this.link(e, n - o, i, o, r), i
            }, t.getTextWidth = function(t) {
                var e = this.internal.getFontSize();
                return this.getStringUnitWidth(t) * e / this.internal.scaleFactor
            }, t.getLineHeight = function() {
                return this.internal.getLineHeight()
            }
        }(e.API), e.API.autoPrint = function() {
            var t;
            return this.internal.events.subscribe("postPutResources", function() {
                t = this.internal.newObject(), this.internal.write("<< /S/Named /Type/Action /N/Print >>", "endobj")
            }), this.internal.events.subscribe("putCatalog", function() {
                this.internal.write("/OpenAction " + t + " 0 R")
            }), this
        },
        /**
         * jsPDF Canvas PlugIn
         * Copyright (c) 2014 Steven Spungin (TwelveTone LLC)  steven@twelvetone.tv
         *
         * Licensed under the MIT License.
         * http://opensource.org/licenses/mit-license
         */
        function(t) {
            t.events.push(["initialized", function() {
                this.canvas.pdf = this
            }]), t.canvas = {
                getContext: function(t) {
                    return this.pdf.context2d._canvas = this, this.pdf.context2d
                },
                style: {}
            }, Object.defineProperty(t.canvas, "width", {
                get: function() {
                    return this._width
                },
                set: function(t) {
                    this._width = t, this.getContext("2d").pageWrapX = t + 1
                }
            }), Object.defineProperty(t.canvas, "height", {
                get: function() {
                    return this._height
                },
                set: function(t) {
                    this._height = t, this.getContext("2d").pageWrapY = t + 1
                }
            })
        }(e.API),
        /** ====================================================================
         * jsPDF Cell plugin
         * Copyright (c) 2013 Youssef Beddad, youssef.beddad@gmail.com
         *               2013 Eduardo Menezes de Morais, eduardo.morais@usp.br
         *               2013 Lee Driscoll, https://github.com/lsdriscoll
         *               2014 Juan Pablo Gaviria, https://github.com/juanpgaviria
         *               2014 James Hall, james@parall.ax
         *               2014 Diego Casorran, https://github.com/diegocr
         *
         * 
         * ====================================================================
         */
        function(t) {
            var e, n, r, i, o = {
                    x: void 0,
                    y: void 0,
                    w: void 0,
                    h: void 0,
                    ln: void 0
                },
                a = 1,
                s = function(t, e, n, r, i) {
                    o = {
                        x: t,
                        y: e,
                        w: n,
                        h: r,
                        ln: i
                    }
                },
                c = function() {
                    return o
                },
                l = {
                    left: 0,
                    top: 0,
                    bottom: 0
                };
            t.setHeaderFunction = function(t) {
                i = t
            }, t.getTextDimensions = function(t) {
                e = this.internal.getFont().fontName, n = this.table_font_size || this.internal.getFontSize(), r = this.internal.getFont().fontStyle;
                var i, o;
                (o = document.createElement("font")).id = "jsPDFCell";
                try {
                    o.style.fontStyle = r
                } catch (t) {
                    o.style.fontWeight = r
                }
                o.style.fontName = e, o.style.fontSize = n + "pt";
                try {
                    o.textContent = t
                } catch (e) {
                    o.innerText = t
                }
                return document.body.appendChild(o), i = {
                    w: (o.offsetWidth + 1) * (19.049976 / 25.4),
                    h: (o.offsetHeight + 1) * (19.049976 / 25.4)
                }, document.body.removeChild(o), i
            }, t.cellAddPage = function() {
                var t = this.margins || l;
                this.addPage(), s(t.left, t.top, void 0, void 0), a += 1
            }, t.cellInitialize = function() {
                o = {
                    x: void 0,
                    y: void 0,
                    w: void 0,
                    h: void 0,
                    ln: void 0
                }, a = 1
            }, t.cell = function(t, e, n, r, i, o, a) {
                var u = c(),
                    h = !1;
                if (void 0 !== u.ln)
                    if (u.ln === o) t = u.x + u.w, e = u.y;
                    else {
                        var f = this.margins || l;
                        u.y + u.h + r + 13 >= this.internal.pageSize.height - f.bottom && (this.cellAddPage(), h = !0, this.printHeaders && this.tableHeaderRow && this.printHeaderRow(o, !0)), e = c().y + c().h, h && (e = 23)
                    }
                if (void 0 !== i[0])
                    if (this.printingHeaderRow ? this.rect(t, e, n, r, "FD") : this.rect(t, e, n, r), "right" === a) {
                        i instanceof Array || (i = [i]);
                        for (var d = 0; d < i.length; d++) {
                            var p = i[d],
                                m = this.getStringUnitWidth(p) * this.internal.getFontSize();
                            this.text(p, t + n - m - 3, e + this.internal.getLineHeight() * (d + 1))
                        }
                    } else this.text(i, t + 3, e + this.internal.getLineHeight());
                return s(t, e, n, r, o), this
            }, t.arrayMax = function(t, e) {
                var n, r, i, o = t[0];
                for (n = 0, r = t.length; n < r; n += 1) i = t[n], e ? -1 === e(o, i) && (o = i) : i > o && (o = i);
                return o
            }, t.table = function(e, n, r, i, s) {
                if (!r) throw "No data for PDF table";
                var c, u, h, f, d, p, m, g, w, y, v = [],
                    b = [],
                    x = {},
                    k = {},
                    _ = [],
                    S = [],
                    C = !1,
                    A = !0,
                    q = 12,
                    T = l;
                if (T.width = this.internal.pageSize.width, s && (!0 === s.autoSize && (C = !0), !1 === s.printHeaders && (A = !1), s.fontSize && (q = s.fontSize), s.css && void 0 !== s.css["font-size"] && (q = 16 * s.css["font-size"]), s.margins && (T = s.margins)), this.lnMod = 0, o = {
                        x: void 0,
                        y: void 0,
                        w: void 0,
                        h: void 0,
                        ln: void 0
                    }, a = 1, this.printHeaders = A, this.margins = T, this.setFontSize(q), this.table_font_size = q, void 0 === i || null === i) v = Object.keys(r[0]);
                else if (i[0] && "string" != typeof i[0]) {
                    for (u = 0, h = i.length; u < h; u += 1) c = i[u], v.push(c.name), b.push(c.prompt), k[c.name] = c.width * (19.049976 / 25.4)
                } else v = i;
                if (C)
                    for (y = function(t) {
                            return t[c]
                        }, u = 0, h = v.length; u < h; u += 1) {
                        for (x[c = v[u]] = r.map(y), _.push(this.getTextDimensions(b[u] || c).w), m = 0, f = (p = x[c]).length; m < f; m += 1) d = p[m], _.push(this.getTextDimensions(d).w);
                        k[c] = t.arrayMax(_), _ = []
                    }
                if (A) {
                    P = this.calculateLineHeight(v, k, b.length ? b : v);
                    for (u = 0, h = v.length; u < h; u += 1) c = v[u], S.push([e, n, k[c], P, String(b.length ? b[u] : c)]);
                    this.setTableHeaderRow(S), this.printHeaderRow(1, !1)
                }
                for (u = 0, h = r.length; u < h; u += 1) {
                    var P;
                    for (g = r[u], P = this.calculateLineHeight(v, k, g), m = 0, w = v.length; m < w; m += 1) c = v[m], this.cell(e, n, k[c], P, g[c], u + 2, c.align)
                }
                return this.lastCellPos = o, this.table_x = e, this.table_y = n, this
            }, t.calculateLineHeight = function(t, e, n) {
                for (var r, i = 0, o = 0; o < t.length; o++) {
                    n[r = t[o]] = this.splitTextToSize(String(n[r]), e[r] - 3);
                    var a = this.internal.getLineHeight() * n[r].length + 3;
                    a > i && (i = a)
                }
                return i
            }, t.setTableHeaderRow = function(t) {
                this.tableHeaderRow = t
            }, t.printHeaderRow = function(t, e) {
                if (!this.tableHeaderRow) throw "Property tableHeaderRow does not exist.";
                var n, r, o, c;
                if (this.printingHeaderRow = !0, void 0 !== i) {
                    var l = i(this, a);
                    s(l[0], l[1], l[2], l[3], -1)
                }
                this.setFontStyle("bold");
                var u = [];
                for (o = 0, c = this.tableHeaderRow.length; o < c; o += 1) this.setFillColor(200, 200, 200), n = this.tableHeaderRow[o], e && (this.margins.top = 9, n[1] = this.margins && this.margins.top || 0, u.push(n)), r = [].concat(n), this.cell.apply(this, r.concat(t));
                u.length > 0 && this.setTableHeaderRow(u), this.setFontStyle("normal"), this.printingHeaderRow = !1
            }
        }(e.API),
        /**
         * jsPDF Context2D PlugIn Copyright (c) 2014 Steven Spungin (TwelveTone LLC) steven@twelvetone.tv
         *
         * Licensed under the MIT License. http://opensource.org/licenses/mit-license
         */
        function(t) {
            function e() {
                this._isStrokeTransparent = !1, this._strokeOpacity = 1, this.strokeStyle = "#000000", this.fillStyle = "#000000", this._isFillTransparent = !1, this._fillOpacity = 1, this.font = "12pt times", this.textBaseline = "alphabetic", this.textAlign = "start", this.lineWidth = 1, this.lineJoin = "miter", this.lineCap = "butt", this._transform = [1, 0, 0, 1, 0, 0], this.globalCompositeOperation = "normal", this.globalAlpha = 1, this._clip_path = [], this.ignoreClearRect = !1, this.copy = function(t) {
                    this._isStrokeTransparent = t._isStrokeTransparent, this._strokeOpacity = t._strokeOpacity, this.strokeStyle = t.strokeStyle, this._isFillTransparent = t._isFillTransparent, this._fillOpacity = t._fillOpacity, this.fillStyle = t.fillStyle, this.font = t.font, this.lineWidth = t.lineWidth, this.lineJoin = t.lineJoin, this.lineCap = t.lineCap, this.textBaseline = t.textBaseline, this.textAlign = t.textAlign, this._fontSize = t._fontSize, this._transform = t._transform.slice(0), this.globalCompositeOperation = t.globalCompositeOperation, this.globalAlpha = t.globalAlpha, this._clip_path = t._clip_path.slice(0), this.ignoreClearRect = t.ignoreClearRect
                }
            }
            t.events.push(["initialized", function() {
                this.context2d.pdf = this, this.context2d.internal.pdf = this, this.context2d.ctx = new e, this.context2d.ctxStack = [], this.context2d.path = []
            }]), t.context2d = {
                pageWrapXEnabled: !1,
                pageWrapYEnabled: !1,
                pageWrapX: 9999999,
                pageWrapY: 9999999,
                ctx: new e,
                f2: function(t) {
                    return t.toFixed(2)
                },
                fillRect: function(t, e, n, r) {
                    if (!this._isFillTransparent()) {
                        t = this._wrapX(t), e = this._wrapY(e);
                        var i = this._matrix_map_rect(this.ctx._transform, {
                            x: t,
                            y: e,
                            w: n,
                            h: r
                        });
                        this.pdf.rect(i.x, i.y, i.w, i.h, "f")
                    }
                },
                strokeRect: function(t, e, n, r) {
                    if (!this._isStrokeTransparent()) {
                        t = this._wrapX(t), e = this._wrapY(e);
                        var i = this._matrix_map_rect(this.ctx._transform, {
                            x: t,
                            y: e,
                            w: n,
                            h: r
                        });
                        this.pdf.rect(i.x, i.y, i.w, i.h, "s")
                    }
                },
                clearRect: function(t, e, n, r) {
                    if (!this.ctx.ignoreClearRect) {
                        t = this._wrapX(t), e = this._wrapY(e);
                        var i = this._matrix_map_rect(this.ctx._transform, {
                            x: t,
                            y: e,
                            w: n,
                            h: r
                        });
                        this.save(), this.setFillStyle("#ffffff"), this.pdf.rect(i.x, i.y, i.w, i.h, "f"), this.restore()
                    }
                },
                save: function() {
                    this.ctx._fontSize = this.pdf.internal.getFontSize();
                    var t = new e;
                    t.copy(this.ctx), this.ctxStack.push(this.ctx), this.ctx = t
                },
                restore: function() {
                    this.ctx = this.ctxStack.pop(), this.setFillStyle(this.ctx.fillStyle), this.setStrokeStyle(this.ctx.strokeStyle), this.setFont(this.ctx.font), this.pdf.setFontSize(this.ctx._fontSize), this.setLineCap(this.ctx.lineCap), this.setLineWidth(this.ctx.lineWidth), this.setLineJoin(this.ctx.lineJoin)
                },
                rect: function(t, e, n, r) {
                    this.moveTo(t, e), this.lineTo(t + n, e), this.lineTo(t + n, e + r), this.lineTo(t, e + r), this.lineTo(t, e), this.closePath()
                },
                beginPath: function() {
                    this.path = []
                },
                closePath: function() {
                    this.path.push({
                        type: "close"
                    })
                },
                _getRGBA: function(t) {
                    var e, n, r, o;
                    if (!t) return {
                        r: 0,
                        g: 0,
                        b: 0,
                        a: 0,
                        style: t
                    };
                    if (this.internal.rxTransparent.test(t)) e = 0, n = 0, r = 0, o = 0;
                    else {
                        var a = this.internal.rxRgb.exec(t);
                        null != a ? (e = parseInt(a[1]), n = parseInt(a[2]), r = parseInt(a[3]), o = 1) : null != (a = this.internal.rxRgba.exec(t)) ? (e = parseInt(a[1]), n = parseInt(a[2]), r = parseInt(a[3]), o = parseFloat(a[4])) : (o = 1, "#" != t.charAt(0) && ((t = i.colorNameToHex(t)) || (t = "#000000")), 4 === t.length ? (e = t.substring(1, 2), e += e, n = t.substring(2, 3), n += n, r = t.substring(3, 4), r += r) : (e = t.substring(1, 3), n = t.substring(3, 5), r = t.substring(5, 7)), e = parseInt(e, 16), n = parseInt(n, 16), r = parseInt(r, 16))
                    }
                    return {
                        r: e,
                        g: n,
                        b: r,
                        a: o,
                        style: t
                    }
                },
                setFillStyle: function(t) {
                    var e = this._getRGBA(t);
                    this.ctx.fillStyle = t, this.ctx._isFillTransparent = 0 === e.a, this.ctx._fillOpacity = e.a, this.pdf.setFillColor(e.r, e.g, e.b, {
                        a: e.a
                    }), this.pdf.setTextColor(e.r, e.g, e.b, {
                        a: e.a
                    })
                },
                setStrokeStyle: function(t) {
                    var e = this._getRGBA(t);
                    this.ctx.strokeStyle = e.style, this.ctx._isStrokeTransparent = 0 === e.a, this.ctx._strokeOpacity = e.a, 0 === e.a ? this.pdf.setDrawColor(255, 255, 255) : (e.a, this.pdf.setDrawColor(e.r, e.g, e.b))
                },
                fillText: function(t, e, n, r) {
                    if (!this._isFillTransparent()) {
                        e = this._wrapX(e), n = this._wrapY(n);
                        var i = this._matrix_map_point(this.ctx._transform, [e, n]);
                        e = i[0], n = i[1];
                        var o = 57.2958 * this._matrix_rotation(this.ctx._transform);
                        if (this.ctx._clip_path.length > 0) {
                            var a;
                            (a = window.outIntercept ? "group" === window.outIntercept.type ? window.outIntercept.stream : window.outIntercept : this.internal.getCurrentPage()).push("q");
                            var s = this.path;
                            this.path = this.ctx._clip_path, this.ctx._clip_path = [], this._fill(null, !0), this.ctx._clip_path = this.path, this.path = s
                        }
                        var c = 1;
                        try {
                            c = this._matrix_decompose(this._getTransform()).scale[0]
                        } catch (t) {
                            console.warn(t)
                        }
                        if (c < .01) this.pdf.text(t, e, this._getBaseline(n), null, o);
                        else {
                            var l = this.pdf.internal.getFontSize();
                            this.pdf.setFontSize(l * c), this.pdf.text(t, e, this._getBaseline(n), null, o), this.pdf.setFontSize(l)
                        }
                        this.ctx._clip_path.length > 0 && a.push("Q")
                    }
                },
                strokeText: function(t, e, n, r) {
                    if (!this._isStrokeTransparent()) {
                        e = this._wrapX(e), n = this._wrapY(n);
                        var i = this._matrix_map_point(this.ctx._transform, [e, n]);
                        e = i[0], n = i[1];
                        var o = 57.2958 * this._matrix_rotation(this.ctx._transform);
                        if (this.ctx._clip_path.length > 0) {
                            var a;
                            (a = window.outIntercept ? "group" === window.outIntercept.type ? window.outIntercept.stream : window.outIntercept : this.internal.getCurrentPage()).push("q");
                            var s = this.path;
                            this.path = this.ctx._clip_path, this.ctx._clip_path = [], this._fill(null, !0), this.ctx._clip_path = this.path, this.path = s
                        }
                        var c = 1;
                        try {
                            c = this._matrix_decompose(this._getTransform()).scale[0]
                        } catch (t) {
                            console.warn(t)
                        }
                        if (1 === c) this.pdf.text(t, e, this._getBaseline(n), {
                            stroke: !0
                        }, o);
                        else {
                            var l = this.pdf.internal.getFontSize();
                            this.pdf.setFontSize(l * c), this.pdf.text(t, e, this._getBaseline(n), {
                                stroke: !0
                            }, o), this.pdf.setFontSize(l)
                        }
                        this.ctx._clip_path.length > 0 && a.push("Q")
                    }
                },
                setFont: function(t) {
                    this.ctx.font = t;
                    var e = /\s*(\w+)\s+(\w+)\s+(\w+)\s+([\d\.]+)(px|pt|em)\s+(.*)?/;
                    if (null != (u = e.exec(t))) {
                        var n = u[1],
                            r = u[3],
                            i = u[4],
                            o = u[5],
                            a = u[6];
                        i = "px" === o ? Math.floor(parseFloat(i)) : "em" === o ? Math.floor(parseFloat(i) * this.pdf.getFontSize()) : Math.floor(parseFloat(i)), this.pdf.setFontSize(i), "bold" === r || "700" === r ? this.pdf.setFontStyle("bold") : "italic" === n ? this.pdf.setFontStyle("italic") : this.pdf.setFontStyle("normal");
                        var s, c = (f = a).toLowerCase().split(/\s*,\s*/);
                        s = -1 != c.indexOf("arial") ? "Arial" : -1 != c.indexOf("verdana") ? "Verdana" : -1 != c.indexOf("helvetica") ? "Helvetica" : -1 != c.indexOf("sans-serif") ? "sans-serif" : -1 != c.indexOf("fixed") ? "Fixed" : -1 != c.indexOf("monospace") ? "Monospace" : -1 != c.indexOf("terminal") ? "Terminal" : -1 != c.indexOf("courier") ? "Courier" : -1 != c.indexOf("times") ? "Times" : -1 != c.indexOf("cursive") ? "Cursive" : -1 != c.indexOf("fantasy") ? "Fantasy" : (c.indexOf("serif"), "Serif");
                        var l;
                        l = "bold" === r ? "bold" : "normal", this.pdf.setFont(s, l)
                    } else {
                        var u = (e = /\s*(\d+)(pt|px|em)\s+([\w "]+)\s*([\w "]+)?/).exec(t);
                        if (null != u) {
                            var h = u[1],
                                f = u[3];
                            (l = u[4]) || (l = "normal"), h = "em" === o ? Math.floor(parseFloat(i) * this.pdf.getFontSize()) : Math.floor(parseFloat(h)), this.pdf.setFontSize(h), this.pdf.setFont(f, l)
                        }
                    }
                },
                setTextBaseline: function(t) {
                    this.ctx.textBaseline = t
                },
                getTextBaseline: function() {
                    return this.ctx.textBaseline
                },
                setTextAlign: function(t) {
                    this.ctx.textAlign = t
                },
                getTextAlign: function() {
                    return this.ctx.textAlign
                },
                setLineWidth: function(t) {
                    this.ctx.lineWidth = t, this.pdf.setLineWidth(t)
                },
                setLineCap: function(t) {
                    this.ctx.lineCap = t, this.pdf.setLineCap(t)
                },
                setLineJoin: function(t) {
                    this.ctx.lineJoin = t, this.pdf.setLineJoin(t)
                },
                moveTo: function(t, e) {
                    t = this._wrapX(t), e = this._wrapY(e);
                    var n = this._matrix_map_point(this.ctx._transform, [t, e]),
                        r = {
                            type: "mt",
                            x: t = n[0],
                            y: e = n[1]
                        };
                    this.path.push(r)
                },
                _wrapX: function(t) {
                    return this.pageWrapXEnabled ? t % this.pageWrapX : t
                },
                _wrapY: function(t) {
                    return this.pageWrapYEnabled ? (this._gotoPage(this._page(t)), (t - this.lastBreak) % this.pageWrapY) : t
                },
                transform: function(t, e, n, r, i, o) {
                    this.ctx._transform = [t, e, n, r, i, o]
                },
                setTransform: function(t, e, n, r, i, o) {
                    this.ctx._transform = [t, e, n, r, i, o]
                },
                _getTransform: function() {
                    return this.ctx._transform
                },
                lastBreak: 0,
                pageBreaks: [],
                _page: function(t) {
                    if (this.pageWrapYEnabled) {
                        this.lastBreak = 0;
                        for (var e = 0, n = 0, r = 0; r < this.pageBreaks.length; r++)
                            if (t >= this.pageBreaks[r]) {
                                e++, 0 === this.lastBreak && n++;
                                var i = this.pageBreaks[r] - this.lastBreak;
                                this.lastBreak = this.pageBreaks[r], n += o = Math.floor(i / this.pageWrapY)
                            }
                        if (0 === this.lastBreak) {
                            var o = Math.floor(t / this.pageWrapY) + 1;
                            n += o
                        }
                        return n + e
                    }
                    return this.pdf.internal.getCurrentPageInfo().pageNumber
                },
                _gotoPage: function(t) {},
                lineTo: function(t, e) {
                    t = this._wrapX(t), e = this._wrapY(e);
                    var n = this._matrix_map_point(this.ctx._transform, [t, e]),
                        r = {
                            type: "lt",
                            x: t = n[0],
                            y: e = n[1]
                        };
                    this.path.push(r)
                },
                bezierCurveTo: function(t, e, n, r, i, o) {
                    t = this._wrapX(t), e = this._wrapY(e), n = this._wrapX(n), r = this._wrapY(r), i = this._wrapX(i), o = this._wrapY(o);
                    var a;
                    i = (a = this._matrix_map_point(this.ctx._transform, [i, o]))[0], o = a[1];
                    var s = {
                        type: "bct",
                        x1: t = (a = this._matrix_map_point(this.ctx._transform, [t, e]))[0],
                        y1: e = a[1],
                        x2: n = (a = this._matrix_map_point(this.ctx._transform, [n, r]))[0],
                        y2: r = a[1],
                        x: i,
                        y: o
                    };
                    this.path.push(s)
                },
                quadraticCurveTo: function(t, e, n, r) {
                    t = this._wrapX(t), e = this._wrapY(e), n = this._wrapX(n), r = this._wrapY(r);
                    var i;
                    n = (i = this._matrix_map_point(this.ctx._transform, [n, r]))[0], r = i[1];
                    var o = {
                        type: "qct",
                        x1: t = (i = this._matrix_map_point(this.ctx._transform, [t, e]))[0],
                        y1: e = i[1],
                        x: n,
                        y: r
                    };
                    this.path.push(o)
                },
                arc: function(t, e, n, r, i, o) {
                    if (t = this._wrapX(t), e = this._wrapY(e), !this._matrix_is_identity(this.ctx._transform)) {
                        var a = this._matrix_map_point(this.ctx._transform, [t, e]);
                        t = a[0], e = a[1];
                        var s = this._matrix_map_point(this.ctx._transform, [0, 0]),
                            c = this._matrix_map_point(this.ctx._transform, [0, n]);
                        n = Math.sqrt(Math.pow(c[0] - s[0], 2) + Math.pow(c[1] - s[1], 2))
                    }
                    var l = {
                        type: "arc",
                        x: t,
                        y: e,
                        radius: n,
                        startAngle: r,
                        endAngle: i,
                        anticlockwise: o
                    };
                    this.path.push(l)
                },
                drawImage: function(t, e, n, r, i, o, a, s, c) {
                    void 0 !== o && (e = o, n = a, r = s, i = c), e = this._wrapX(e), n = this._wrapY(n);
                    var l, u = this._matrix_map_rect(this.ctx._transform, {
                            x: e,
                            y: n,
                            w: r,
                            h: i
                        }),
                        h = (this._matrix_map_rect(this.ctx._transform, {
                            x: o,
                            y: a,
                            w: s,
                            h: c
                        }), /data:image\/(\w+).*/i.exec(t));
                    l = null != h ? h[1] : "png", this.pdf.addImage(t, l, u.x, u.y, u.w, u.h)
                },
                _matrix_multiply: function(t, e) {
                    var n = e[0],
                        r = e[1],
                        i = e[2],
                        o = e[3],
                        a = e[4],
                        s = e[5],
                        c = n * t[0] + r * t[2],
                        l = i * t[0] + o * t[2],
                        u = a * t[0] + s * t[2] + t[4];
                    return r = n * t[1] + r * t[3], o = i * t[1] + o * t[3], s = a * t[1] + s * t[3] + t[5], n = c, i = l, a = u, [n, r, i, o, a, s]
                },
                _matrix_rotation: function(t) {
                    return Math.atan2(t[2], t[0])
                },
                _matrix_decompose: function(t) {
                    var e = t[0],
                        n = t[1],
                        r = t[2],
                        i = t[3],
                        o = Math.sqrt(e * e + n * n),
                        a = (e /= o) * r + (n /= o) * i;
                    r -= e * a, i -= n * a;
                    var s = Math.sqrt(r * r + i * i);
                    return r /= s, i /= s, a /= s, e * i < n * r && (e = -e, n = -n, a = -a, o = -o), {
                        scale: [o, 0, 0, s, 0, 0],
                        translate: [1, 0, 0, 1, t[4], t[5]],
                        rotate: [e, n, -n, e, 0, 0],
                        skew: [1, 0, a, 1, 0, 0]
                    }
                },
                _matrix_map_point: function(t, e) {
                    var n = t[0],
                        r = t[1],
                        i = t[2],
                        o = t[3],
                        a = t[4],
                        s = t[5],
                        c = e[0],
                        l = e[1];
                    return [c * n + l * i + a, c * r + l * o + s]
                },
                _matrix_map_point_obj: function(t, e) {
                    var n = this._matrix_map_point(t, [e.x, e.y]);
                    return {
                        x: n[0],
                        y: n[1]
                    }
                },
                _matrix_map_rect: function(t, e) {
                    var n = this._matrix_map_point(t, [e.x, e.y]),
                        r = this._matrix_map_point(t, [e.x + e.w, e.y + e.h]);
                    return {
                        x: n[0],
                        y: n[1],
                        w: r[0] - n[0],
                        h: r[1] - n[1]
                    }
                },
                _matrix_is_identity: function(t) {
                    return 1 == t[0] && (0 == t[1] && (0 == t[2] && (1 == t[3] && (0 == t[4] && 0 == t[5]))))
                },
                rotate: function(t) {
                    var e = [Math.cos(t), Math.sin(t), -Math.sin(t), Math.cos(t), 0, 0];
                    this.ctx._transform = this._matrix_multiply(this.ctx._transform, e)
                },
                scale: function(t, e) {
                    var n = [t, 0, 0, e, 0, 0];
                    this.ctx._transform = this._matrix_multiply(this.ctx._transform, n)
                },
                translate: function(t, e) {
                    var n = [1, 0, 0, 1, t, e];
                    this.ctx._transform = this._matrix_multiply(this.ctx._transform, n)
                },
                stroke: function() {
                    if (this.ctx._clip_path.length > 0) {
                        var t;
                        (t = window.outIntercept ? "group" === window.outIntercept.type ? window.outIntercept.stream : window.outIntercept : this.internal.getCurrentPage()).push("q");
                        var e = this.path;
                        this.path = this.ctx._clip_path, this.ctx._clip_path = [], this._stroke(!0), this.ctx._clip_path = this.path, this.path = e, this._stroke(!1), t.push("Q")
                    } else this._stroke(!1)
                },
                _stroke: function(t) {
                    if (t || !this._isStrokeTransparent()) {
                        for (var e = [], n = this.path, r = 0; r < n.length; r++) {
                            var i = n[r];
                            switch (i.type) {
                                case "mt":
                                    e.push({
                                        start: i,
                                        deltas: [],
                                        abs: []
                                    });
                                    break;
                                case "lt":
                                    h = [i.x - n[r - 1].x, i.y - n[r - 1].y];
                                    e[e.length - 1].deltas.push(h), e[e.length - 1].abs.push(i);
                                    break;
                                case "bct":
                                    h = [i.x1 - n[r - 1].x, i.y1 - n[r - 1].y, i.x2 - n[r - 1].x, i.y2 - n[r - 1].y, i.x - n[r - 1].x, i.y - n[r - 1].y];
                                    e[e.length - 1].deltas.push(h);
                                    break;
                                case "qct":
                                    var o = n[r - 1].x + 2 / 3 * (i.x1 - n[r - 1].x),
                                        a = n[r - 1].y + 2 / 3 * (i.y1 - n[r - 1].y),
                                        s = i.x + 2 / 3 * (i.x1 - i.x),
                                        c = i.y + 2 / 3 * (i.y1 - i.y),
                                        l = i.x,
                                        u = i.y,
                                        h = [o - n[r - 1].x, a - n[r - 1].y, s - n[r - 1].x, c - n[r - 1].y, l - n[r - 1].x, u - n[r - 1].y];
                                    e[e.length - 1].deltas.push(h);
                                    break;
                                case "arc":
                                    0 == e.length && e.push({
                                        start: {
                                            x: 0,
                                            y: 0
                                        },
                                        deltas: [],
                                        abs: []
                                    }), e[e.length - 1].arc = !0, e[e.length - 1].abs.push(i)
                            }
                        }
                        for (r = 0; r < e.length; r++) {
                            var f;
                            if (f = r == e.length - 1 ? "s" : null, e[r].arc)
                                for (var d = e[r].abs, p = 0; p < d.length; p++) {
                                    var m = d[p],
                                        g = 360 * m.startAngle / (2 * Math.PI),
                                        w = 360 * m.endAngle / (2 * Math.PI),
                                        y = m.x,
                                        v = m.y;
                                    this.internal.arc2(this, y, v, m.radius, g, w, m.anticlockwise, f, t)
                                } else {
                                    var y = e[r].start.x,
                                        v = e[r].start.y;
                                    t ? (this.pdf.lines(e[r].deltas, y, v, null, null), this.pdf.clip_fixed()) : this.pdf.lines(e[r].deltas, y, v, null, f)
                                }
                        }
                    }
                },
                _isFillTransparent: function() {
                    return this.ctx._isFillTransparent || 0 == this.globalAlpha
                },
                _isStrokeTransparent: function() {
                    return this.ctx._isStrokeTransparent || 0 == this.globalAlpha
                },
                fill: function(t) {
                    if (this.ctx._clip_path.length > 0) {
                        var e;
                        (e = window.outIntercept ? "group" === window.outIntercept.type ? window.outIntercept.stream : window.outIntercept : this.internal.getCurrentPage()).push("q");
                        var n = this.path;
                        this.path = this.ctx._clip_path, this.ctx._clip_path = [], this._fill(t, !0), this.ctx._clip_path = this.path, this.path = n, this._fill(t, !1), e.push("Q")
                    } else this._fill(t, !1)
                },
                _fill: function(t, e) {
                    if (!this._isFillTransparent()) {
                        var r, i = "function" == typeof this.pdf.internal.newObject2;
                        r = window.outIntercept ? "group" === window.outIntercept.type ? window.outIntercept.stream : window.outIntercept : this.internal.getCurrentPage();
                        var o = [],
                            a = window.outIntercept;
                        if (i) switch (this.ctx.globalCompositeOperation) {
                            case "normal":
                            case "source-over":
                                break;
                            case "destination-in":
                            case "destination-out":
                                var s = this.pdf.internal.newStreamObject(),
                                    c = this.pdf.internal.newObject2();
                                c.push("<</Type /ExtGState"), c.push("/SMask <</S /Alpha /G " + s.objId + " 0 R>>"), c.push(">>");
                                d = "MASK" + c.objId;
                                this.pdf.internal.addGraphicsState(d, c.objId);
                                var l = "/" + d + " gs";
                                r.splice(0, 0, "q"), r.splice(1, 0, l), r.push("Q"), window.outIntercept = s;
                                break;
                            default:
                                var u = "/" + this.pdf.internal.blendModeMap[this.ctx.globalCompositeOperation.toUpperCase()];
                                u && this.pdf.internal.out(u + " gs")
                        }
                        var h = this.ctx.globalAlpha;
                        if (this.ctx._fillOpacity < 1 && (h = this.ctx._fillOpacity), i) {
                            var f = this.pdf.internal.newObject2();
                            f.push("<</Type /ExtGState"), f.push("/CA " + h), f.push("/ca " + h), f.push(">>");
                            var d = "GS_O_" + f.objId;
                            this.pdf.internal.addGraphicsState(d, f.objId), this.pdf.internal.out("/" + d + " gs")
                        }
                        for (var p = this.path, m = 0; m < p.length; m++) {
                            var g = p[m];
                            switch (g.type) {
                                case "mt":
                                    o.push({
                                        start: g,
                                        deltas: [],
                                        abs: []
                                    });
                                    break;
                                case "lt":
                                    _ = [g.x - p[m - 1].x, g.y - p[m - 1].y];
                                    o[o.length - 1].deltas.push(_), o[o.length - 1].abs.push(g);
                                    break;
                                case "bct":
                                    _ = [g.x1 - p[m - 1].x, g.y1 - p[m - 1].y, g.x2 - p[m - 1].x, g.y2 - p[m - 1].y, g.x - p[m - 1].x, g.y - p[m - 1].y];
                                    o[o.length - 1].deltas.push(_);
                                    break;
                                case "qct":
                                    var w = p[m - 1].x + 2 / 3 * (g.x1 - p[m - 1].x),
                                        y = p[m - 1].y + 2 / 3 * (g.y1 - p[m - 1].y),
                                        v = g.x + 2 / 3 * (g.x1 - g.x),
                                        b = g.y + 2 / 3 * (g.y1 - g.y),
                                        x = g.x,
                                        k = g.y,
                                        _ = [w - p[m - 1].x, y - p[m - 1].y, v - p[m - 1].x, b - p[m - 1].y, x - p[m - 1].x, k - p[m - 1].y];
                                    o[o.length - 1].deltas.push(_);
                                    break;
                                case "arc":
                                    0 === o.length && o.push({
                                        deltas: [],
                                        abs: []
                                    }), o[o.length - 1].arc = !0, o[o.length - 1].abs.push(g);
                                    break;
                                case "close":
                                    o.push({
                                        close: !0
                                    })
                            }
                        }
                        for (m = 0; m < o.length; m++) {
                            var S;
                            if (m == o.length - 1 ? (S = "f", "evenodd" === t && (S += "*")) : S = null, o[m].close) this.pdf.internal.out("h"), this.pdf.internal.out("f");
                            else if (o[m].arc) {
                                o[m].start && this.internal.move2(this, o[m].start.x, o[m].start.y);
                                for (var C = o[m].abs, A = 0; A < C.length; A++) {
                                    var q = C[A];
                                    if (void 0 !== q.startAngle) {
                                        var T = 360 * q.startAngle / (2 * Math.PI),
                                            P = 360 * q.endAngle / (2 * Math.PI),
                                            I = q.x,
                                            E = q.y;
                                        if (0 === A && this.internal.move2(this, I, E), this.internal.arc2(this, I, E, q.radius, T, P, q.anticlockwise, null, e), A === C.length - 1 && o[m].start) {
                                            var I = o[m].start.x,
                                                E = o[m].start.y;
                                            this.internal.line2(n, I, E)
                                        }
                                    } else this.internal.line2(n, q.x, q.y)
                                }
                            } else {
                                var I = o[m].start.x,
                                    E = o[m].start.y;
                                e ? (this.pdf.lines(o[m].deltas, I, E, null, null), this.pdf.clip_fixed()) : this.pdf.lines(o[m].deltas, I, E, null, S)
                            }
                        }
                        window.outIntercept = a
                    }
                },
                pushMask: function() {
                    if ("function" == typeof this.pdf.internal.newObject2) {
                        var t = this.pdf.internal.newStreamObject(),
                            e = this.pdf.internal.newObject2();
                        e.push("<</Type /ExtGState"), e.push("/SMask <</S /Alpha /G " + t.objId + " 0 R>>"), e.push(">>");
                        var n = "MASK" + e.objId;
                        this.pdf.internal.addGraphicsState(n, e.objId);
                        var r = "/" + n + " gs";
                        this.pdf.internal.out(r)
                    } else console.log("jsPDF v2 not enabled")
                },
                clip: function() {
                    if (this.ctx._clip_path.length > 0)
                        for (var t = 0; t < this.path.length; t++) this.ctx._clip_path.push(this.path[t]);
                    else this.ctx._clip_path = this.path;
                    this.path = []
                },
                measureText: function(t) {
                    var e = this.pdf;
                    return {
                        getWidth: function() {
                            var n = e.internal.getFontSize(),
                                r = e.getStringUnitWidth(t) * n / e.internal.scaleFactor;
                            return r *= 1.3333
                        },
                        get width() {
                            return this.getWidth(t)
                        }
                    }
                },
                _getBaseline: function(t) {
                    var e = parseInt(this.pdf.internal.getFontSize()),
                        n = .25 * e;
                    switch (this.ctx.textBaseline) {
                        case "bottom":
                            return t - n;
                        case "top":
                            return t + e;
                        case "hanging":
                            return t + e - n;
                        case "middle":
                            return t + e / 2 - n;
                        case "ideographic":
                            return t;
                        case "alphabetic":
                        default:
                            return t
                    }
                }
            };
            var n = t.context2d;
            Object.defineProperty(n, "fillStyle", {
                set: function(t) {
                    this.setFillStyle(t)
                },
                get: function() {
                    return this.ctx.fillStyle
                }
            }), Object.defineProperty(n, "strokeStyle", {
                set: function(t) {
                    this.setStrokeStyle(t)
                },
                get: function() {
                    return this.ctx.strokeStyle
                }
            }), Object.defineProperty(n, "lineWidth", {
                set: function(t) {
                    this.setLineWidth(t)
                },
                get: function() {
                    return this.ctx.lineWidth
                }
            }), Object.defineProperty(n, "lineCap", {
                set: function(t) {
                    this.setLineCap(t)
                },
                get: function() {
                    return this.ctx.lineCap
                }
            }), Object.defineProperty(n, "lineJoin", {
                set: function(t) {
                    this.setLineJoin(t)
                },
                get: function() {
                    return this.ctx.lineJoin
                }
            }), Object.defineProperty(n, "miterLimit", {
                set: function(t) {
                    this.ctx.miterLimit = t
                },
                get: function() {
                    return this.ctx.miterLimit
                }
            }), Object.defineProperty(n, "textBaseline", {
                set: function(t) {
                    this.setTextBaseline(t)
                },
                get: function() {
                    return this.getTextBaseline()
                }
            }), Object.defineProperty(n, "textAlign", {
                set: function(t) {
                    this.setTextAlign(t)
                },
                get: function() {
                    return this.getTextAlign()
                }
            }), Object.defineProperty(n, "font", {
                set: function(t) {
                    this.setFont(t)
                },
                get: function() {
                    return this.ctx.font
                }
            }), Object.defineProperty(n, "globalCompositeOperation", {
                set: function(t) {
                    this.ctx.globalCompositeOperation = t
                },
                get: function() {
                    return this.ctx.globalCompositeOperation
                }
            }), Object.defineProperty(n, "globalAlpha", {
                set: function(t) {
                    this.ctx.globalAlpha = t
                },
                get: function() {
                    return this.ctx.globalAlpha
                }
            }), Object.defineProperty(n, "ignoreClearRect", {
                set: function(t) {
                    this.ctx.ignoreClearRect = t
                },
                get: function() {
                    return this.ctx.ignoreClearRect
                }
            }), n.internal = {}, n.internal.rxRgb = /rgb\s*\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*\)/, n.internal.rxRgba = /rgba\s*\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*,\s*([\d\.]+)\s*\)/, n.internal.rxTransparent = /transparent|rgba\s*\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*,\s*0+\s*\)/, n.internal.arc = function(t, e, n, r, i, o, a, s) {
                for (var c = this.pdf.internal.scaleFactor, l = this.pdf.internal.pageSize.height, u = this.pdf.internal.f2, h = i * (Math.PI / 180), f = o * (Math.PI / 180), d = this.createArc(r, h, f, a), p = 0; p < d.length; p++) {
                    var m = d[p];
                    0 === p ? this.pdf.internal.out([u((m.x1 + e) * c), u((l - (m.y1 + n)) * c), "m", u((m.x2 + e) * c), u((l - (m.y2 + n)) * c), u((m.x3 + e) * c), u((l - (m.y3 + n)) * c), u((m.x4 + e) * c), u((l - (m.y4 + n)) * c), "c"].join(" ")) : this.pdf.internal.out([u((m.x2 + e) * c), u((l - (m.y2 + n)) * c), u((m.x3 + e) * c), u((l - (m.y3 + n)) * c), u((m.x4 + e) * c), u((l - (m.y4 + n)) * c), "c"].join(" ")), t._lastPoint = {
                        x: e,
                        y: n
                    }
                }
                null !== s && this.pdf.internal.out(this.pdf.internal.getStyle(s))
            }, n.internal.arc2 = function(t, e, n, r, i, o, a, s, c) {
                var l = e,
                    u = n;
                c ? (this.arc(t, l, u, r, i, o, a, null), this.pdf.clip_fixed()) : this.arc(t, l, u, r, i, o, a, s)
            }, n.internal.move2 = function(t, e, n) {
                var r = this.pdf.internal.scaleFactor,
                    i = this.pdf.internal.pageSize.height,
                    o = this.pdf.internal.f2;
                this.pdf.internal.out([o(e * r), o((i - n) * r), "m"].join(" ")), t._lastPoint = {
                    x: e,
                    y: n
                }
            }, n.internal.line2 = function(t, e, n) {
                var r = this.pdf.internal.scaleFactor,
                    i = this.pdf.internal.pageSize.height,
                    o = this.pdf.internal.f2,
                    a = {
                        x: e,
                        y: n
                    };
                this.pdf.internal.out([o(a.x * r), o((i - a.y) * r), "l"].join(" ")), t._lastPoint = a
            }, n.internal.createArc = function(t, e, n, r) {
                var i = 2 * Math.PI,
                    o = Math.PI / 2,
                    a = e;
                for ((a < i || a > i) && (a %= i), a < 0 && (a = i + a); e > n;) e -= i;
                var s = Math.abs(n - e);
                s < i && r && (s = i - s);
                for (var c = [], l = r ? -1 : 1, u = a; s > 1e-5;) {
                    var h = u + l * Math.min(s, o);
                    c.push(this.createSmallArc(t, u, h)), s -= Math.abs(h - u), u = h
                }
                return c
            }, n.internal.getCurrentPage = function() {
                return this.pdf.internal.pages[this.pdf.internal.getCurrentPageInfo().pageNumber]
            }, n.internal.createSmallArc = function(t, e, n) {
                var r = (n - e) / 2,
                    i = t * Math.cos(r),
                    o = t * Math.sin(r),
                    a = i,
                    s = -o,
                    c = a * a + s * s,
                    l = c + a * i + s * o,
                    u = 4 / 3 * (Math.sqrt(2 * c * l) - l) / (a * o - s * i),
                    h = a - u * s,
                    f = s + u * a,
                    d = h,
                    p = -f,
                    m = r + e,
                    g = Math.cos(m),
                    w = Math.sin(m);
                return {
                    x1: t * Math.cos(e),
                    y1: t * Math.sin(e),
                    x2: h * g - f * w,
                    y2: h * w + f * g,
                    x3: d * g - p * w,
                    y3: d * w + p * g,
                    x4: t * Math.cos(n),
                    y4: t * Math.sin(n)
                }
            }
        }(e.API),
        /** @preserve
         * jsPDF fromHTML plugin. BETA stage. API subject to change. Needs browser
         * Copyright (c) 2012 Willow Systems Corporation, willow-systems.com
         *               2014 Juan Pablo Gaviria, https://github.com/juanpgaviria
         *               2014 Diego Casorran, https://github.com/diegocr
         *               2014 Daniel Husar, https://github.com/danielhusar
         *               2014 Wolfgang Gassler, https://github.com/woolfg
         *               2014 Steven Spungin, https://github.com/flamenco
         *
         * 
         * ====================================================================
         */
        function(e) {
            var n, r, o, a, s, c, l, u, h, f, d, p, m, g, w, y, v, b, x, k;
            n = function() {
                function t() {}
                return function(e) {
                    return t.prototype = e, new t
                }
            }(), f = function(t) {
                var e, n, r, i, o, a, s;
                for (n = 0, r = t.length, e = void 0, i = !1, a = !1; !i && n !== r;)(e = t[n] = t[n].trimLeft()) && (i = !0), n++;
                for (n = r - 1; r && !a && -1 !== n;)(e = t[n] = t[n].trimRight()) && (a = !0), n--;
                for (o = /\s+$/g, s = !0, n = 0; n !== r;) "\u2028" != t[n] && (e = t[n].replace(/\s+/g, " "), s && (e = e.trimLeft()), e && (s = o.test(e)), t[n] = e), n++;
                return t
            }, p = function(t) {
                var e, n, r;
                for (e = void 0, n = (r = t.split(",")).shift(); !e && n;) e = o[n.trim().toLowerCase()], n = r.shift();
                return e
            }, m = function(t) {
                (t = "auto" === t ? "0px" : t).indexOf("em") > -1 && !isNaN(Number(t.replace("em", ""))) && (t = 18.719 * Number(t.replace("em", "")) + "px"), t.indexOf("pt") > -1 && !isNaN(Number(t.replace("pt", ""))) && (t = 1.333 * Number(t.replace("pt", "")) + "px");
                var e;
                return void 0, 16, (e = g[t]) ? e : void 0 !== (e = {
                    "xx-small": 9,
                    "x-small": 11,
                    small: 13,
                    medium: 16,
                    large: 19,
                    "x-large": 23,
                    "xx-large": 28,
                    auto: 0
                }[{
                    css_line_height_string: t
                }]) ? g[t] = e / 16 : (e = parseFloat(t)) ? g[t] = e / 16 : 3 === (e = t.match(/([\d\.]+)(px)/)).length ? g[t] = parseFloat(e[1]) / 16 : g[t] = 1
            }, h = function(t) {
                var e, n, r;
                return r = function(e) {
                    var n;
                    return n = function(t) {
                            return document.defaultView && document.defaultView.getComputedStyle ? document.defaultView.getComputedStyle(t, null) : t.currentStyle ? t.currentStyle : t.style
                        }(t),
                        function(t) {
                            return t = t.replace(/-\D/g, function(t) {
                                return t.charAt(1).toUpperCase()
                            }), n[t]
                        }
                }(), e = {}, n = void 0, e["font-family"] = p(r("font-family")) || "times", e["font-style"] = a[r("font-style")] || "normal", e["text-align"] = s[r("text-align")] || "left", "bold" === (n = c[r("font-weight")] || "normal") && ("normal" === e["font-style"] ? e["font-style"] = n : e["font-style"] = n + e["font-style"]), e["font-size"] = m(r("font-size")) || 1, e["line-height"] = m(r("line-height")) || 1, e.display = "inline" === r("display") ? "inline" : "block", n = "block" === e.display, e["margin-top"] = n && m(r("margin-top")) || 0, e["margin-bottom"] = n && m(r("margin-bottom")) || 0, e["padding-top"] = n && m(r("padding-top")) || 0, e["padding-bottom"] = n && m(r("padding-bottom")) || 0, e["margin-left"] = n && m(r("margin-left")) || 0, e["margin-right"] = n && m(r("margin-right")) || 0, e["padding-left"] = n && m(r("padding-left")) || 0, e["padding-right"] = n && m(r("padding-right")) || 0, e["page-break-before"] = r("page-break-before") || "auto", e.float = l[r("cssFloat")] || "none", e.clear = u[r("clear")] || "none", e.color = r("color"), e
            }, w = function(t, e, n) {
                var r, i, o, a, s;
                if (o = !1, i = void 0, a = void 0, r = n["#" + t.id])
                    if ("function" == typeof r) o = r(t, e);
                    else
                        for (i = 0, a = r.length; !o && i !== a;) o = r[i](t, e), i++;
                if (r = n[t.nodeName], !o && r)
                    if ("function" == typeof r) o = r(t, e);
                    else
                        for (i = 0, a = r.length; !o && i !== a;) o = r[i](t, e), i++;
                for (s = t.className ? t.className.split(" ") : [], i = 0; i < s.length; i++)
                    if (r = n["." + s[i]], !o && r)
                        if ("function" == typeof r) o = r(t, e);
                        else
                            for (i = 0, a = r.length; !o && i !== a;) o = r[i](t, e), i++;
                return o
            }, k = function(t, e) {
                var n, r, i, o, a, s, c, l, u;
                for (n = [], r = [], i = 0, u = t.rows[0].cells.length, c = t.clientWidth; i < u;) l = t.rows[0].cells[i], r[i] = {
                    name: l.textContent.toLowerCase().replace(/\s+/g, ""),
                    prompt: l.textContent.replace(/\r?\n/g, ""),
                    width: l.clientWidth / c * e.pdf.internal.pageSize.width
                }, i++;
                for (i = 1; i < t.rows.length;) {
                    for (s = t.rows[i], a = {}, o = 0; o < s.cells.length;) a[r[o].name] = s.cells[o].textContent.replace(/\r?\n/g, ""), o++;
                    n.push(a), i++
                }
                return {
                    rows: n,
                    headers: r
                }
            };
            var _ = {
                    SCRIPT: 1,
                    STYLE: 1,
                    NOSCRIPT: 1,
                    OBJECT: 1,
                    EMBED: 1,
                    SELECT: 1
                },
                S = 1;
            r = function(e, i, o) {
                var a, s, c, l, u, f, d, p;
                for (s = e.childNodes, a = void 0, (u = "block" === (c = h(e)).display) && (i.setBlockBoundary(), i.setBlockStyle(c)), l = 0, f = s.length; l < f;) {
                    if ("object" === (void 0 === (a = s[l]) ? "undefined" : t(a))) {
                        if (i.executeWatchFunctions(a), 1 === a.nodeType && "HEADER" === a.nodeName) {
                            var m = a,
                                g = i.pdf.margins_doc.top;
                            i.pdf.internal.events.subscribe("addPage", function(t) {
                                i.y = g, r(m, i, o), i.pdf.margins_doc.top = i.y + 10, i.y += 10
                            }, !1)
                        }
                        if (8 === a.nodeType && "#comment" === a.nodeName) ~a.textContent.indexOf("ADD_PAGE") && (i.pdf.addPage(), i.y = i.pdf.margins_doc.top);
                        else if (1 !== a.nodeType || _[a.nodeName])
                            if (3 === a.nodeType) {
                                var v = a.nodeValue;
                                if (a.nodeValue && "LI" === a.parentNode.nodeName)
                                    if ("OL" === a.parentNode.parentNode.nodeName) v = S++ + ". " + v;
                                    else {
                                        var b = c["font-size"],
                                            x = (3 - .75 * b) * i.pdf.internal.scaleFactor,
                                            C = .75 * b * i.pdf.internal.scaleFactor,
                                            A = 1.74 * b / i.pdf.internal.scaleFactor;
                                        p = function(t, e) {
                                            this.pdf.circle(t + x, e + C, A, "FD")
                                        }
                                    }
                                16 & a.ownerDocument.body.compareDocumentPosition(a) && i.addText(v, c)
                            } else "string" == typeof a && i.addText(a, c);
                        else {
                            var q;
                            if ("IMG" === a.nodeName) {
                                var T = a.getAttribute("src");
                                q = y[i.pdf.sHashCode(T) || T]
                            }
                            if (q) {
                                i.pdf.internal.pageSize.height - i.pdf.margins_doc.bottom < i.y + a.height && i.y > i.pdf.margins_doc.top && (i.pdf.addPage(), i.y = i.pdf.margins_doc.top, i.executeWatchFunctions(a));
                                var P = h(a),
                                    I = i.x,
                                    E = 12 / i.pdf.internal.scaleFactor,
                                    O = (P["margin-left"] + P["padding-left"]) * E,
                                    B = (P["margin-right"] + P["padding-right"]) * E,
                                    F = (P["margin-top"] + P["padding-top"]) * E,
                                    R = (P["margin-bottom"] + P["padding-bottom"]) * E;
                                void 0 !== P.float && "right" === P.float ? I += i.settings.width - a.width - B : I += O, i.pdf.addImage(q, I, i.y + F, a.width, a.height), q = void 0, "right" === P.float || "left" === P.float ? (i.watchFunctions.push(function(t, e, n, r) {
                                    return i.y >= e ? (i.x += t, i.settings.width += n, !0) : !!(r && 1 === r.nodeType && !_[r.nodeName] && i.x + r.width > i.pdf.margins_doc.left + i.pdf.margins_doc.width) && (i.x += t, i.y = e, i.settings.width += n, !0)
                                }.bind(this, "left" === P.float ? -a.width - O - B : 0, i.y + a.height + F + R, a.width)), i.watchFunctions.push(function(t, e, n) {
                                    return !(i.y < t && e === i.pdf.internal.getNumberOfPages()) || 1 === n.nodeType && "both" === h(n).clear && (i.y = t, !0)
                                }.bind(this, i.y + a.height, i.pdf.internal.getNumberOfPages())), i.settings.width -= a.width + O + B, "left" === P.float && (i.x += a.width + O + B)) : i.y += a.height + F + R
                            } else if ("TABLE" === a.nodeName) d = k(a, i), i.y += 10, i.pdf.table(i.x, i.y, d.rows, d.headers, {
                                autoSize: !1,
                                printHeaders: o.printHeaders,
                                margins: i.pdf.margins_doc,
                                css: h(a)
                            }), i.y = i.pdf.lastCellPos.y + i.pdf.lastCellPos.h + 20;
                            else if ("OL" === a.nodeName || "UL" === a.nodeName) S = 1, w(a, i, o) || r(a, i, o), i.y += 10;
                            else if ("LI" === a.nodeName) {
                                var D = i.x;
                                i.x += 20 / i.pdf.internal.scaleFactor, i.y += 3, w(a, i, o) || r(a, i, o), i.x = D
                            } else "BR" === a.nodeName ? (i.y += c["font-size"] * i.pdf.internal.scaleFactor, i.addText("\u2028", n(c))) : w(a, i, o) || r(a, i, o)
                        }
                    }
                    l++
                }
                if (o.outY = i.y, u) return i.setBlockBoundary(p)
            }, y = {}, v = function(t, e, n, r) {
                function i() {
                    e.pdf.internal.events.publish("imagesLoaded"), r(o)
                }
                for (var o, a = t.getElementsByTagName("img"), s = a.length, c = 0; s--;) ! function(t, n, r) {
                    if (t) {
                        var a = new Image;
                        o = ++c, a.crossOrigin = "", a.onerror = a.onload = function() {
                            if (a.complete && (0 === a.src.indexOf("data:image/") && (a.width = n || a.width || 0, a.height = r || a.height || 0), a.width + a.height)) {
                                var o = e.pdf.sHashCode(t) || t;
                                y[o] = y[o] || a
                            }--c || i()
                        }, a.src = t
                    }
                }(a[s].getAttribute("src"), a[s].width, a[s].height);
                return c || i()
            }, b = function(t, e, n) {
                var i = t.getElementsByTagName("footer");
                if (i.length > 0) {
                    i = i[0];
                    var o = e.pdf.internal.write,
                        a = e.y;
                    e.pdf.internal.write = function() {}, r(i, e, n);
                    var s = Math.ceil(e.y - a) + 5;
                    e.y = a, e.pdf.internal.write = o, e.pdf.margins_doc.bottom += s;
                    for (var c = function(t) {
                            var o = void 0 !== t ? t.pageNumber : 1,
                                a = e.y;
                            e.y = e.pdf.internal.pageSize.height - e.pdf.margins_doc.bottom, e.pdf.margins_doc.bottom -= s;
                            for (var c = i.getElementsByTagName("span"), l = 0; l < c.length; ++l)(" " + c[l].className + " ").replace(/[\n\t]/g, " ").indexOf(" pageCounter ") > -1 && (c[l].innerHTML = o), (" " + c[l].className + " ").replace(/[\n\t]/g, " ").indexOf(" totalPages ") > -1 && (c[l].innerHTML = "###jsPDFVarTotalPages###");
                            r(i, e, n), e.pdf.margins_doc.bottom += s, e.y = a
                        }, l = i.getElementsByTagName("span"), u = 0; u < l.length; ++u)(" " + l[u].className + " ").replace(/[\n\t]/g, " ").indexOf(" totalPages ") > -1 && e.pdf.internal.events.subscribe("htmlRenderingFinished", e.pdf.putTotalPages.bind(e.pdf, "###jsPDFVarTotalPages###"), !0);
                    e.pdf.internal.events.subscribe("addPage", c, !1), c(), _.FOOTER = 1
                }
            }, x = function(t, e, n, i, o, a) {
                if (!e) return !1;
                "string" == typeof e || e.parentNode || (e = "" + e.innerHTML), "string" == typeof e && (e = function(t) {
                    var e, n, r, i;
                    return r = "jsPDFhtmlText" + Date.now().toString() + (1e3 * Math.random()).toFixed(0), i = "position: absolute !important;clip: rect(1px 1px 1px 1px); /* IE6, IE7 */clip: rect(1px, 1px, 1px, 1px);padding:0 !important;border:0 !important;height: 1px !important;width: 1px !important; top:auto;left:-100px;overflow: hidden;", n = document.createElement("div"), n.style.cssText = i, n.innerHTML = '<iframe style="height:1px;width:1px" name="' + r + '" />', document.body.appendChild(n), (e = window.frames[r]).document.open(), e.document.writeln(t), e.document.close(), e.document.body
                }(e.replace(/<\/?script[^>]*?>/gi, "")));
                var s, c = new d(t, n, i, o);
                return v.call(this, e, c, o.elementHandlers, function(t) {
                    b(e, c, o.elementHandlers), r(e, c, o.elementHandlers), c.pdf.internal.events.publish("htmlRenderingFinished"), s = c.dispose(), "function" == typeof a ? a(s) : t && console.error("jsPDF Warning: rendering issues? provide a callback to fromHTML!")
                }), s || {
                    x: c.x,
                    y: c.y
                }
            }, (d = function(t, e, n, r) {
                return this.pdf = t, this.x = e, this.y = n, this.settings = r, this.watchFunctions = [], this.init(), this
            }).prototype.init = function() {
                return this.paragraph = {
                    text: [],
                    style: []
                }, this.pdf.internal.write("q")
            }, d.prototype.dispose = function() {
                return this.pdf.internal.write("Q"), {
                    x: this.x,
                    y: this.y,
                    ready: !0
                }
            }, d.prototype.executeWatchFunctions = function(t) {
                var e = !1,
                    n = [];
                if (this.watchFunctions.length > 0) {
                    for (var r = 0; r < this.watchFunctions.length; ++r) !0 === this.watchFunctions[r](t) ? e = !0 : n.push(this.watchFunctions[r]);
                    this.watchFunctions = n
                }
                return e
            }, d.prototype.splitFragmentsIntoLines = function(t, e) {
                var r, i, o, a, s, c, l, u, h, f, d, p, m, g;
                for (12, f = this.pdf.internal.scaleFactor, a = {}, i = void 0, h = void 0, o = void 0, s = void 0, g = void 0, u = void 0, l = void 0, c = void 0, p = [d = []], r = 0, m = this.settings.width; t.length;)
                    if (s = t.shift(), g = e.shift(), s)
                        if (i = g["font-family"], h = g["font-style"], (o = a[i + h]) || (o = this.pdf.internal.getFont(i, h).metadata.Unicode, a[i + h] = o), u = {
                                widths: o.widths,
                                kerning: o.kerning,
                                fontSize: 12 * g["font-size"],
                                textIndent: r
                            }, l = this.pdf.getStringUnitWidth(s, u) * u.fontSize / f, "\u2028" == s) d = [], p.push(d);
                        else if (r + l > m) {
                    for (c = this.pdf.splitTextToSize(s, m, u), d.push([c.shift(), g]); c.length;) d = [
                        [c.shift(), g]
                    ], p.push(d);
                    r = this.pdf.getStringUnitWidth(d[0][0], u) * u.fontSize / f
                } else d.push([s, g]), r += l;
                if (void 0 !== g["text-align"] && ("center" === g["text-align"] || "right" === g["text-align"] || "justify" === g["text-align"]))
                    for (var w = 0; w < p.length; ++w) {
                        var y = this.pdf.getStringUnitWidth(p[w][0][0], u) * u.fontSize / f;
                        w > 0 && (p[w][0][1] = n(p[w][0][1]));
                        var v = m - y;
                        if ("right" === g["text-align"]) p[w][0][1]["margin-left"] = v;
                        else if ("center" === g["text-align"]) p[w][0][1]["margin-left"] = v / 2;
                        else if ("justify" === g["text-align"]) {
                            var b = p[w][0][0].split(" ").length - 1;
                            p[w][0][1]["word-spacing"] = v / b, w === p.length - 1 && (p[w][0][1]["word-spacing"] = 0)
                        }
                    }
                return p
            }, d.prototype.RenderTextFragment = function(t, e) {
                var n, r;
                r = 0, this.pdf.internal.pageSize.height - this.pdf.margins_doc.bottom < this.y + this.pdf.internal.getFontSize() && (this.pdf.internal.write("ET", "Q"), this.pdf.addPage(), this.y = this.pdf.margins_doc.top, this.pdf.internal.write("q", "BT", this.getPdfColor(e.color), this.pdf.internal.getCoordinateString(this.x), this.pdf.internal.getVerticalCoordinateString(this.y), "Td"), r = Math.max(r, e["line-height"], e["font-size"]), this.pdf.internal.write(0, (-12 * r).toFixed(2), "Td")), n = this.pdf.internal.getFont(e["font-family"], e["font-style"]);
                var i = this.getPdfColor(e.color);
                i !== this.lastTextColor && (this.pdf.internal.write(i), this.lastTextColor = i), void 0 !== e["word-spacing"] && e["word-spacing"] > 0 && this.pdf.internal.write(e["word-spacing"].toFixed(2), "Tw"), this.pdf.internal.write("/" + n.id, (12 * e["font-size"]).toFixed(2), "Tf", "(" + this.pdf.internal.pdfEscape(t) + ") Tj"), void 0 !== e["word-spacing"] && this.pdf.internal.write(0, "Tw")
            }, d.prototype.getPdfColor = function(t) {
                var e, n, r, o = /rgb\s*\(\s*(\d+),\s*(\d+),\s*(\d+\s*)\)/.exec(t);
                if (null != o ? (e = parseInt(o[1]), n = parseInt(o[2]), r = parseInt(o[3])) : ("#" != t.charAt(0) && ((t = i.colorNameToHex(t)) || (t = "#000000")), e = t.substring(1, 3), e = parseInt(e, 16), n = t.substring(3, 5), n = parseInt(n, 16), r = t.substring(5, 7), r = parseInt(r, 16)), "string" == typeof e && /^#[0-9A-Fa-f]{6}$/.test(e)) {
                    var a = parseInt(e.substr(1), 16);
                    e = a >> 16 & 255, n = a >> 8 & 255, r = 255 & a
                }
                var s = this.f3;
                return 0 === e && 0 === n && 0 === r || void 0 === n ? s(e / 255) + " g" : [s(e / 255), s(n / 255), s(r / 255), "rg"].join(" ")
            }, d.prototype.f3 = function(t) {
                return t.toFixed(3)
            }, d.prototype.renderParagraph = function(t) {
                var e, n, r, i, o, a, s, c, l, u, h, d, p;
                if (r = f(this.paragraph.text), d = this.paragraph.style, e = this.paragraph.blockstyle, this.paragraph = {
                        text: [],
                        style: [],
                        blockstyle: {},
                        priorblockstyle: e
                    }, r.join("").trim()) {
                    s = this.splitFragmentsIntoLines(r, d), a = void 0, c = void 0, 12, n = 12 / this.pdf.internal.scaleFactor, this.priorMarginBottom = this.priorMarginBottom || 0, h = (Math.max((e["margin-top"] || 0) - this.priorMarginBottom, 0) + (e["padding-top"] || 0)) * n, u = ((e["margin-bottom"] || 0) + (e["padding-bottom"] || 0)) * n, this.priorMarginBottom = e["margin-bottom"] || 0, "always" === e["page-break-before"] && (this.pdf.addPage(), this.y = 0, h = ((e["margin-top"] || 0) + (e["padding-top"] || 0)) * n), l = this.pdf.internal.write, i = void 0, o = void 0, this.y += h, l("q", "BT 0 g", this.pdf.internal.getCoordinateString(this.x), this.pdf.internal.getVerticalCoordinateString(this.y), "Td");
                    for (var m = 0; s.length;) {
                        for (c = 0, i = 0, o = (a = s.shift()).length; i !== o;) a[i][0].trim() && (c = Math.max(c, a[i][1]["line-height"], a[i][1]["font-size"]), p = 7 * a[i][1]["font-size"]), i++;
                        var g = 0,
                            w = 0;
                        for (void 0 !== a[0][1]["margin-left"] && a[0][1]["margin-left"] > 0 && (g = (w = this.pdf.internal.getCoordinateString(a[0][1]["margin-left"])) - m, m = w), l(g + Math.max(e["margin-left"] || 0, 0) * n, (-12 * c).toFixed(2), "Td"), i = 0, o = a.length; i !== o;) a[i][0] && this.RenderTextFragment(a[i][0], a[i][1]), i++;
                        if (this.y += c * n, this.executeWatchFunctions(a[0][1]) && s.length > 0) {
                            var y = [],
                                v = [];
                            s.forEach(function(t) {
                                for (var e = 0, n = t.length; e !== n;) t[e][0] && (y.push(t[e][0] + " "), v.push(t[e][1])), ++e
                            }), s = this.splitFragmentsIntoLines(f(y), v), l("ET", "Q"), l("q", "BT 0 g", this.pdf.internal.getCoordinateString(this.x), this.pdf.internal.getVerticalCoordinateString(this.y), "Td")
                        }
                    }
                    return t && "function" == typeof t && t.call(this, this.x - 9, this.y - p / 2), l("ET", "Q"), this.y += u
                }
            }, d.prototype.setBlockBoundary = function(t) {
                return this.renderParagraph(t)
            }, d.prototype.setBlockStyle = function(t) {
                return this.paragraph.blockstyle = t
            }, d.prototype.addText = function(t, e) {
                return this.paragraph.text.push(t), this.paragraph.style.push(e)
            }, o = {
                helvetica: "helvetica",
                "sans-serif": "helvetica",
                "times new roman": "times",
                serif: "times",
                times: "times",
                monospace: "courier",
                courier: "courier"
            }, c = {
                100: "normal",
                200: "normal",
                300: "normal",
                400: "normal",
                500: "bold",
                600: "bold",
                700: "bold",
                800: "bold",
                900: "bold",
                normal: "normal",
                bold: "bold",
                bolder: "bold",
                lighter: "normal"
            }, a = {
                normal: "normal",
                italic: "italic",
                oblique: "italic"
            }, s = {
                left: "left",
                right: "right",
                center: "center",
                justify: "justify"
            }, l = {
                none: "none",
                right: "right",
                left: "left"
            }, u = {
                none: "none",
                both: "both"
            }, g = {
                normal: 1
            }, e.fromHTML = function(t, e, n, r, i, o) {
                return this.margins_doc = o || {
                    top: 0,
                    bottom: 0
                }, r || (r = {}), r.elementHandlers || (r.elementHandlers = {}), x(this, t, isNaN(e) ? 4 : e, isNaN(n) ? 4 : n, r, i)
            }
        }(e.API),
        /** ==================================================================== 
         * jsPDF JavaScript plugin
         * Copyright (c) 2013 Youssef Beddad, youssef.beddad@gmail.com
         * 
         * 
         * ====================================================================
         */
        function(t) {
            var n, r, i;
            e.API.addJS = function(t) {
                return i = t, this.internal.events.subscribe("postPutResources", function(t) {
                    n = this.internal.newObject(), this.internal.write("<< /Names [(EmbeddedJS) " + (n + 1) + " 0 R] >>", "endobj"), r = this.internal.newObject(), this.internal.write("<< /S /JavaScript /JS (", i, ") >>", "endobj")
                }), this.internal.events.subscribe("putCatalog", function() {
                    void 0 !== n && void 0 !== r && this.internal.write("/Names <</JavaScript " + n + " 0 R>>")
                }), this
            }
        }(),
        /**
         * jsPDF Outline PlugIn
         * Copyright (c) 2014 Steven Spungin (TwelveTone LLC)  steven@twelvetone.tv
         *
         * Licensed under the MIT License.
         * http://opensource.org/licenses/mit-license
         */
        function(t) {
            t.events.push(["postPutResources", function() {
                var t = this,
                    e = /^(\d+) 0 obj$/;
                if (this.outline.root.children.length > 0)
                    for (var n = t.outline.render().split(/\r\n/), r = 0; r < n.length; r++) {
                        var i = n[r],
                            o = e.exec(i);
                        if (null != o) {
                            var a = o[1];
                            t.internal.newObjectDeferredBegin(a)
                        }
                        t.internal.write(i)
                    }
                if (this.outline.createNamedDestinations) {
                    for (var s = this.internal.pages.length, c = [], r = 0; r < s; r++) {
                        var l = t.internal.newObject();
                        c.push(l);
                        var u = t.internal.getPageInfo(r + 1);
                        t.internal.write("<< /D[" + u.objId + " 0 R /XYZ null null null]>> endobj")
                    }
                    var h = t.internal.newObject();
                    t.internal.write("<< /Names [ ");
                    for (r = 0; r < c.length; r++) t.internal.write("(page_" + (r + 1) + ")" + c[r] + " 0 R");
                    t.internal.write(" ] >>", "endobj");
                    t.internal.newObject();
                    t.internal.write("<< /Dests " + h + " 0 R"), t.internal.write(">>", "endobj")
                }
            }]), t.events.push(["putCatalog", function() {
                var t = this;
                t.outline.root.children.length > 0 && (t.internal.write("/Outlines", this.outline.makeRef(this.outline.root)), this.outline.createNamedDestinations && t.internal.write("/Names " + namesOid + " 0 R"))
            }]), t.events.push(["initialized", function() {
                var t = this;
                t.outline = {
                    createNamedDestinations: !1,
                    root: {
                        children: []
                    }
                }, t.outline.add = function(t, e, n) {
                    var r = {
                        title: e,
                        options: n,
                        children: []
                    };
                    return null == t && (t = this.root), t.children.push(r), r
                }, t.outline.render = function() {
                    return this.ctx = {}, this.ctx.val = "", this.ctx.pdf = t, this.genIds_r(this.root), this.renderRoot(this.root), this.renderItems(this.root), this.ctx.val
                }, t.outline.genIds_r = function(e) {
                    e.id = t.internal.newObjectDeferred();
                    for (var n = 0; n < e.children.length; n++) this.genIds_r(e.children[n])
                }, t.outline.renderRoot = function(t) {
                    this.objStart(t), this.line("/Type /Outlines"), t.children.length > 0 && (this.line("/First " + this.makeRef(t.children[0])), this.line("/Last " + this.makeRef(t.children[t.children.length - 1]))), this.line("/Count " + this.count_r({
                        count: 0
                    }, t)), this.objEnd()
                }, t.outline.renderItems = function(e) {
                    for (i = 0; i < e.children.length; i++) {
                        o = e.children[i];
                        this.objStart(o), this.line("/Title " + this.makeString(o.title)), this.line("/Parent " + this.makeRef(e)), i > 0 && this.line("/Prev " + this.makeRef(e.children[i - 1])), i < e.children.length - 1 && this.line("/Next " + this.makeRef(e.children[i + 1])), o.children.length > 0 && (this.line("/First " + this.makeRef(o.children[0])), this.line("/Last " + this.makeRef(o.children[o.children.length - 1])));
                        var n = this.count = this.count_r({
                            count: 0
                        }, o);
                        if (n > 0 && this.line("/Count " + n), o.options && o.options.pageNumber) {
                            var r = t.internal.getPageInfo(o.options.pageNumber);
                            this.line("/Dest [" + r.objId + " 0 R /XYZ 0 " + this.ctx.pdf.internal.pageSize.height + " 0]")
                        }
                        this.objEnd()
                    }
                    for (var i = 0; i < e.children.length; i++) {
                        var o = e.children[i];
                        this.renderItems(o)
                    }
                }, t.outline.line = function(t) {
                    this.ctx.val += t + "\r\n"
                }, t.outline.makeRef = function(t) {
                    return t.id + " 0 R"
                }, t.outline.makeString = function(e) {
                    return "(" + t.internal.pdfEscape(e) + ")"
                }, t.outline.objStart = function(t) {
                    this.ctx.val += "\r\n" + t.id + " 0 obj\r\n<<\r\n"
                }, t.outline.objEnd = function(t) {
                    this.ctx.val += ">> \r\nendobj\r\n"
                }, t.outline.count_r = function(t, e) {
                    for (var n = 0; n < e.children.length; n++) t.count++, this.count_r(t, e.children[n]);
                    return t.count
                }
            }])
        }(e.API),
        /**@preserve
         *  ====================================================================
         * jsPDF PNG PlugIn
         * Copyright (c) 2014 James Robb, https://github.com/jamesbrobb
         *
         * 
         * ====================================================================
         */
        function(t) {
            var e = function() {
                    return "function" != typeof PNG || "function" != typeof s
                },
                n = function(e) {
                    return e !== t.image_compression.NONE && r()
                },
                r = function() {
                    var t = "function" == typeof o;
                    if (!t) throw new Error("requires deflate.js for compression");
                    return t
                },
                i = function(e, n, r, i) {
                    var s = 5,
                        u = f;
                    switch (i) {
                        case t.image_compression.FAST:
                            s = 3, u = h;
                            break;
                        case t.image_compression.MEDIUM:
                            s = 6, u = d;
                            break;
                        case t.image_compression.SLOW:
                            s = 9, u = p
                    }
                    e = l(e, n, r, u);
                    var m = new Uint8Array(a(s)),
                        g = c(e),
                        w = new o(s),
                        y = w.append(e),
                        v = w.flush(),
                        b = m.length + y.length + v.length,
                        x = new Uint8Array(b + 4);
                    return x.set(m), x.set(y, m.length), x.set(v, m.length + y.length), x[b++] = g >>> 24 & 255, x[b++] = g >>> 16 & 255, x[b++] = g >>> 8 & 255, x[b++] = 255 & g, t.arrayBufferToBinaryString(x)
                },
                a = function(t, e) {
                    var n = Math.LOG2E * Math.log(32768) - 8 << 4 | 8,
                        r = n << 8;
                    return r |= Math.min(3, (e - 1 & 255) >> 1) << 6, r |= 0, r += 31 - r % 31, [n, 255 & r]
                },
                c = function(t, e) {
                    for (var n, r = 1, i = 0, o = t.length, a = 0; o > 0;) {
                        o -= n = o > e ? e : o;
                        do {
                            i += r += t[a++]
                        } while (--n);
                        r %= 65521, i %= 65521
                    }
                    return (i << 16 | r) >>> 0
                },
                l = function(t, e, n, r) {
                    for (var i, o, a, s = t.length / e, c = new Uint8Array(t.length + s), l = g(), u = 0; u < s; u++) {
                        if (a = u * e, i = t.subarray(a, a + e), r) c.set(r(i, n, o), a + u);
                        else {
                            for (var h = 0, f = l.length, d = []; h < f; h++) d[h] = l[h](i, n, o);
                            var p = w(d.concat());
                            c.set(d[p], a + u)
                        }
                        o = i
                    }
                    return c
                },
                u = function(t, e, n) {
                    var r = Array.apply([], t);
                    return r.unshift(0), r
                },
                h = function(t, e, n) {
                    var r, i = [],
                        o = 0,
                        a = t.length;
                    for (i[0] = 1; o < a; o++) r = t[o - e] || 0, i[o + 1] = t[o] - r + 256 & 255;
                    return i
                },
                f = function(t, e, n) {
                    var r, i = [],
                        o = 0,
                        a = t.length;
                    for (i[0] = 2; o < a; o++) r = n && n[o] || 0, i[o + 1] = t[o] - r + 256 & 255;
                    return i
                },
                d = function(t, e, n) {
                    var r, i, o = [],
                        a = 0,
                        s = t.length;
                    for (o[0] = 3; a < s; a++) r = t[a - e] || 0, i = n && n[a] || 0, o[a + 1] = t[a] + 256 - (r + i >>> 1) & 255;
                    return o
                },
                p = function(t, e, n) {
                    var r, i, o, a, s = [],
                        c = 0,
                        l = t.length;
                    for (s[0] = 4; c < l; c++) r = t[c - e] || 0, i = n && n[c] || 0, o = n && n[c - e] || 0, a = m(r, i, o), s[c + 1] = t[c] - a + 256 & 255;
                    return s
                },
                m = function(t, e, n) {
                    var r = t + e - n,
                        i = Math.abs(r - t),
                        o = Math.abs(r - e),
                        a = Math.abs(r - n);
                    return i <= o && i <= a ? t : o <= a ? e : n
                },
                g = function() {
                    return [u, h, f, d, p]
                },
                w = function(t) {
                    for (var e, n, r, i = 0, o = t.length; i < o;)((e = y(t[i].slice(1))) < n || !n) && (n = e, r = i), i++;
                    return r
                },
                y = function(t) {
                    for (var e = 0, n = t.length, r = 0; e < n;) r += Math.abs(t[e++]);
                    return r
                },
                v = function(e) {
                    var n;
                    switch (e) {
                        case t.image_compression.FAST:
                            n = 11;
                            break;
                        case t.image_compression.MEDIUM:
                            n = 13;
                            break;
                        case t.image_compression.SLOW:
                            n = 14;
                            break;
                        default:
                            n = 12
                    }
                    return n
                };
            t.processPNG = function(t, r, o, a, s) {
                var c, l, u, h, f, d, p = this.color_spaces.DEVICE_RGB,
                    m = this.decode.FLATE_DECODE,
                    g = 8;
                if (this.isArrayBuffer(t) && (t = new Uint8Array(t)), this.isArrayBufferView(t)) {
                    if (e()) throw new Error("PNG support requires png.js and zlib.js");
                    if (c = new PNG(t), t = c.imgData, g = c.bits, p = c.colorSpace, h = c.colors, -1 !== [4, 6].indexOf(c.colorType)) {
                        if (8 === c.bits)
                            for (var w, y = (I = 32 == c.pixelBitlength ? new Uint32Array(c.decodePixels().buffer) : 16 == c.pixelBitlength ? new Uint16Array(c.decodePixels().buffer) : new Uint8Array(c.decodePixels().buffer)).length, b = new Uint8Array(y * c.colors), x = new Uint8Array(y), k = c.pixelBitlength - c.bits, _ = 0, S = 0; _ < y; _++) {
                                for (C = I[_], w = 0; w < k;) b[S++] = C >>> w & 255, w += c.bits;
                                x[_] = C >>> w & 255
                            }
                        if (16 === c.bits) {
                            for (var C, y = (I = new Uint32Array(c.decodePixels().buffer)).length, b = new Uint8Array(y * (32 / c.pixelBitlength) * c.colors), x = new Uint8Array(y * (32 / c.pixelBitlength)), A = c.colors > 1, _ = 0, S = 0, q = 0; _ < y;) C = I[_++], b[S++] = C >>> 0 & 255, A && (b[S++] = C >>> 16 & 255, C = I[_++], b[S++] = C >>> 0 & 255), x[q++] = C >>> 16 & 255;
                            g = 8
                        }
                        n(a) ? (t = i(b, c.width * c.colors, c.colors, a), d = i(x, c.width, 1, a)) : (t = b, d = x, m = null)
                    }
                    if (3 === c.colorType && (p = this.color_spaces.INDEXED, f = c.palette, c.transparency.indexed)) {
                        for (var T = c.transparency.indexed, P = 0, _ = 0, y = T.length; _ < y; ++_) P += T[_];
                        if ((P /= 255) === y - 1 && -1 !== T.indexOf(0)) u = [T.indexOf(0)];
                        else if (P !== y) {
                            for (var I = c.decodePixels(), x = new Uint8Array(I.length), _ = 0, y = I.length; _ < y; _++) x[_] = T[I[_]];
                            d = i(x, c.width, 1)
                        }
                    }
                    var E = v(a);
                    return l = m === this.decode.FLATE_DECODE ? "/Predictor " + E + " /Colors " + h + " /BitsPerComponent " + g + " /Columns " + c.width : "/Colors " + h + " /BitsPerComponent " + g + " /Columns " + c.width, (this.isArrayBuffer(t) || this.isArrayBufferView(t)) && (t = this.arrayBufferToBinaryString(t)), (d && this.isArrayBuffer(d) || this.isArrayBufferView(d)) && (d = this.arrayBufferToBinaryString(d)), this.createImageInfo(t, c.width, c.height, p, g, m, r, o, l, u, f, d, E)
                }
                throw new Error("Unsupported PNG image data, try using JPEG instead.")
            }
        }(e.API), e.API.autoPrint = function() {
            var t;
            return this.internal.events.subscribe("postPutResources", function() {
                t = this.internal.newObject(), this.internal.write("<< /S/Named /Type/Action /N/Print >>", "endobj")
            }), this.internal.events.subscribe("putCatalog", function() {
                this.internal.write("/OpenAction " + t + " 0 R")
            }), this
        },
        /** @preserve
         * jsPDF split_text_to_size plugin - MIT license.
         * Copyright (c) 2012 Willow Systems Corporation, willow-systems.com
         *               2014 Diego Casorran, https://github.com/diegocr
         */
        function(t) {
            var e = t.getCharWidthsArray = function(t, e) {
                    e || (e = {});
                    var n, r, i, o = e.widths ? e.widths : this.internal.getFont().metadata.Unicode.widths,
                        a = o.fof ? o.fof : 1,
                        s = e.kerning ? e.kerning : this.internal.getFont().metadata.Unicode.kerning,
                        c = s.fof ? s.fof : 1,
                        l = 0,
                        u = o[0] || a,
                        h = [];
                    for (n = 0, r = t.length; n < r; n++) i = t.charCodeAt(n), h.push((o[i] || u) / a + (s[i] && s[i][l] || 0) / c), l = i;
                    return h
                },
                n = function(t) {
                    for (var e = t.length, n = 0; e;) n += t[--e];
                    return n
                },
                r = t.getStringUnitWidth = function(t, r) {
                    return n(e.call(this, t, r))
                },
                i = function(t, e, n, r) {
                    for (var i = [], o = 0, a = t.length, s = 0; o !== a && s + e[o] < n;) s += e[o], o++;
                    i.push(t.slice(0, o));
                    var c = o;
                    for (s = 0; o !== a;) s + e[o] > r && (i.push(t.slice(c, o)), s = 0, c = o), s += e[o], o++;
                    return c !== o && i.push(t.slice(c, o)), i
                },
                o = function(t, o, a) {
                    a || (a = {});
                    var s, c, l, u, h, f, d = [],
                        p = [d],
                        m = a.textIndent || 0,
                        g = 0,
                        w = 0,
                        y = t.split(" "),
                        v = e(" ", a)[0];
                    if (f = -1 === a.lineIndent ? y[0].length + 2 : a.lineIndent || 0) {
                        var b = Array(f).join(" "),
                            x = [];
                        y.map(function(t) {
                            (t = t.split(/\s*\n/)).length > 1 ? x = x.concat(t.map(function(t, e) {
                                return (e && t.length ? "\n" : "") + t
                            })) : x.push(t[0])
                        }), y = x, f = r(b, a)
                    }
                    for (l = 0, u = y.length; l < u; l++) {
                        var k = 0;
                        if (s = y[l], f && "\n" == s[0] && (s = s.substr(1), k = 1), c = e(s, a), w = n(c), m + g + w > o || k) {
                            if (w > o) {
                                for (h = i(s, c, o - (m + g), o), d.push(h.shift()), d = [h.pop()]; h.length;) p.push([h.shift()]);
                                w = n(c.slice(s.length - d[0].length))
                            } else d = [s];
                            p.push(d), m = w + f, g = v
                        } else d.push(s), m += g + w, g = v
                    }
                    if (f) _ = function(t, e) {
                        return (e ? b : "") + t.join(" ")
                    };
                    else var _ = function(t) {
                        return t.join(" ")
                    };
                    return p.map(_)
                };
            t.splitTextToSize = function(t, e, n) {
                n || (n = {});
                var r, i = n.fontSize || this.internal.getFontSize(),
                    a = function(t) {
                        var e = {
                                0: 1
                            },
                            n = {};
                        if (t.widths && t.kerning) return {
                            widths: t.widths,
                            kerning: t.kerning
                        };
                        var r = this.internal.getFont(t.fontName, t.fontStyle);
                        return r.metadata.Unicode ? {
                            widths: r.metadata.Unicode.widths || e,
                            kerning: r.metadata.Unicode.kerning || n
                        } : {
                            widths: e,
                            kerning: n
                        }
                    }.call(this, n);
                r = Array.isArray(t) ? t : t.split(/\r?\n/);
                var s = 1 * this.internal.scaleFactor * e / i;
                a.textIndent = n.textIndent ? 1 * n.textIndent * this.internal.scaleFactor / i : 0, a.lineIndent = n.lineIndent;
                var c, l, u = [];
                for (c = 0, l = r.length; c < l; c++) u = u.concat(o(r[c], s, a));
                return u
            }
        }(e.API),
        /** @preserve 
        jsPDF standard_fonts_metrics plugin
        Copyright (c) 2012 Willow Systems Corporation, willow-systems.com
        MIT license.
        */
        function(t) {
            var e = function(t) {
                    for (var e = {}, n = 0; n < "klmnopqrstuvwxyz".length; n++) e["klmnopqrstuvwxyz" [n]] = "0123456789abcdef" [n];
                    var r, i, o, a, s = {},
                        c = 1,
                        l = s,
                        u = [],
                        h = "",
                        f = "",
                        d = t.length - 1;
                    for (n = 1; n != d;) a = t[n], n += 1, "'" == a ? r ? (o = r.join(""), r = void 0) : r = [] : r ? r.push(a) : "{" == a ? (u.push([l, o]), l = {}, o = void 0) : "}" == a ? ((i = u.pop())[0][i[1]] = l, o = void 0, l = i[0]) : "-" == a ? c = -1 : void 0 === o ? e.hasOwnProperty(a) ? (h += e[a], o = parseInt(h, 16) * c, c = 1, h = "") : h += a : e.hasOwnProperty(a) ? (f += e[a], l[o] = parseInt(f, 16) * c, c = 1, o = void 0, f = "") : f += a;
                    return s
                },
                n = {
                    codePages: ["WinAnsiEncoding"],
                    WinAnsiEncoding: e("{19m8n201n9q201o9r201s9l201t9m201u8m201w9n201x9o201y8o202k8q202l8r202m9p202q8p20aw8k203k8t203t8v203u9v2cq8s212m9t15m8w15n9w2dw9s16k8u16l9u17s9z17x8y17y9y}")
                },
                r = {
                    Unicode: {
                        Courier: n,
                        "Courier-Bold": n,
                        "Courier-BoldOblique": n,
                        "Courier-Oblique": n,
                        Helvetica: n,
                        "Helvetica-Bold": n,
                        "Helvetica-BoldOblique": n,
                        "Helvetica-Oblique": n,
                        "Times-Roman": n,
                        "Times-Bold": n,
                        "Times-BoldItalic": n,
                        "Times-Italic": n
                    }
                },
                i = {
                    Unicode: {
                        "Courier-Oblique": e("{'widths'{k3w'fof'6o}'kerning'{'fof'-6o}}"),
                        "Times-BoldItalic": e("{'widths'{k3o2q4ycx2r201n3m201o6o201s2l201t2l201u2l201w3m201x3m201y3m2k1t2l2r202m2n2n3m2o3m2p5n202q6o2r1w2s2l2t2l2u3m2v3t2w1t2x2l2y1t2z1w3k3m3l3m3m3m3n3m3o3m3p3m3q3m3r3m3s3m203t2l203u2l3v2l3w3t3x3t3y3t3z3m4k5n4l4m4m4m4n4m4o4s4p4m4q4m4r4s4s4y4t2r4u3m4v4m4w3x4x5t4y4s4z4s5k3x5l4s5m4m5n3r5o3x5p4s5q4m5r5t5s4m5t3x5u3x5v2l5w1w5x2l5y3t5z3m6k2l6l3m6m3m6n2w6o3m6p2w6q2l6r3m6s3r6t1w6u1w6v3m6w1w6x4y6y3r6z3m7k3m7l3m7m2r7n2r7o1w7p3r7q2w7r4m7s3m7t2w7u2r7v2n7w1q7x2n7y3t202l3mcl4mal2ram3man3mao3map3mar3mas2lat4uau1uav3maw3way4uaz2lbk2sbl3t'fof'6obo2lbp3tbq3mbr1tbs2lbu1ybv3mbz3mck4m202k3mcm4mcn4mco4mcp4mcq5ycr4mcs4mct4mcu4mcv4mcw2r2m3rcy2rcz2rdl4sdm4sdn4sdo4sdp4sdq4sds4sdt4sdu4sdv4sdw4sdz3mek3mel3mem3men3meo3mep3meq4ser2wes2wet2weu2wev2wew1wex1wey1wez1wfl3rfm3mfn3mfo3mfp3mfq3mfr3tfs3mft3rfu3rfv3rfw3rfz2w203k6o212m6o2dw2l2cq2l3t3m3u2l17s3x19m3m}'kerning'{cl{4qu5kt5qt5rs17ss5ts}201s{201ss}201t{cks4lscmscnscoscpscls2wu2yu201ts}201x{2wu2yu}2k{201ts}2w{4qx5kx5ou5qx5rs17su5tu}2x{17su5tu5ou}2y{4qx5kx5ou5qx5rs17ss5ts}'fof'-6ofn{17sw5tw5ou5qw5rs}7t{cksclscmscnscoscps4ls}3u{17su5tu5os5qs}3v{17su5tu5os5qs}7p{17su5tu}ck{4qu5kt5qt5rs17ss5ts}4l{4qu5kt5qt5rs17ss5ts}cm{4qu5kt5qt5rs17ss5ts}cn{4qu5kt5qt5rs17ss5ts}co{4qu5kt5qt5rs17ss5ts}cp{4qu5kt5qt5rs17ss5ts}6l{4qu5ou5qw5rt17su5tu}5q{ckuclucmucnucoucpu4lu}5r{ckuclucmucnucoucpu4lu}7q{cksclscmscnscoscps4ls}6p{4qu5ou5qw5rt17sw5tw}ek{4qu5ou5qw5rt17su5tu}el{4qu5ou5qw5rt17su5tu}em{4qu5ou5qw5rt17su5tu}en{4qu5ou5qw5rt17su5tu}eo{4qu5ou5qw5rt17su5tu}ep{4qu5ou5qw5rt17su5tu}es{17ss5ts5qs4qu}et{4qu5ou5qw5rt17sw5tw}eu{4qu5ou5qw5rt17ss5ts}ev{17ss5ts5qs4qu}6z{17sw5tw5ou5qw5rs}fm{17sw5tw5ou5qw5rs}7n{201ts}fo{17sw5tw5ou5qw5rs}fp{17sw5tw5ou5qw5rs}fq{17sw5tw5ou5qw5rs}7r{cksclscmscnscoscps4ls}fs{17sw5tw5ou5qw5rs}ft{17su5tu}fu{17su5tu}fv{17su5tu}fw{17su5tu}fz{cksclscmscnscoscps4ls}}}"),
                        "Helvetica-Bold": e("{'widths'{k3s2q4scx1w201n3r201o6o201s1w201t1w201u1w201w3m201x3m201y3m2k1w2l2l202m2n2n3r2o3r2p5t202q6o2r1s2s2l2t2l2u2r2v3u2w1w2x2l2y1w2z1w3k3r3l3r3m3r3n3r3o3r3p3r3q3r3r3r3s3r203t2l203u2l3v2l3w3u3x3u3y3u3z3x4k6l4l4s4m4s4n4s4o4s4p4m4q3x4r4y4s4s4t1w4u3r4v4s4w3x4x5n4y4s4z4y5k4m5l4y5m4s5n4m5o3x5p4s5q4m5r5y5s4m5t4m5u3x5v2l5w1w5x2l5y3u5z3r6k2l6l3r6m3x6n3r6o3x6p3r6q2l6r3x6s3x6t1w6u1w6v3r6w1w6x5t6y3x6z3x7k3x7l3x7m2r7n3r7o2l7p3x7q3r7r4y7s3r7t3r7u3m7v2r7w1w7x2r7y3u202l3rcl4sal2lam3ran3rao3rap3rar3ras2lat4tau2pav3raw3uay4taz2lbk2sbl3u'fof'6obo2lbp3xbq3rbr1wbs2lbu2obv3rbz3xck4s202k3rcm4scn4sco4scp4scq6ocr4scs4mct4mcu4mcv4mcw1w2m2zcy1wcz1wdl4sdm4ydn4ydo4ydp4ydq4yds4ydt4sdu4sdv4sdw4sdz3xek3rel3rem3ren3reo3rep3req5ter3res3ret3reu3rev3rew1wex1wey1wez1wfl3xfm3xfn3xfo3xfp3xfq3xfr3ufs3xft3xfu3xfv3xfw3xfz3r203k6o212m6o2dw2l2cq2l3t3r3u2l17s4m19m3r}'kerning'{cl{4qs5ku5ot5qs17sv5tv}201t{2ww4wy2yw}201w{2ks}201x{2ww4wy2yw}2k{201ts201xs}2w{7qs4qu5kw5os5qw5rs17su5tu7tsfzs}2x{5ow5qs}2y{7qs4qu5kw5os5qw5rs17su5tu7tsfzs}'fof'-6o7p{17su5tu5ot}ck{4qs5ku5ot5qs17sv5tv}4l{4qs5ku5ot5qs17sv5tv}cm{4qs5ku5ot5qs17sv5tv}cn{4qs5ku5ot5qs17sv5tv}co{4qs5ku5ot5qs17sv5tv}cp{4qs5ku5ot5qs17sv5tv}6l{17st5tt5os}17s{2kwclvcmvcnvcovcpv4lv4wwckv}5o{2kucltcmtcntcotcpt4lt4wtckt}5q{2ksclscmscnscoscps4ls4wvcks}5r{2ks4ws}5t{2kwclvcmvcnvcovcpv4lv4wwckv}eo{17st5tt5os}fu{17su5tu5ot}6p{17ss5ts}ek{17st5tt5os}el{17st5tt5os}em{17st5tt5os}en{17st5tt5os}6o{201ts}ep{17st5tt5os}es{17ss5ts}et{17ss5ts}eu{17ss5ts}ev{17ss5ts}6z{17su5tu5os5qt}fm{17su5tu5os5qt}fn{17su5tu5os5qt}fo{17su5tu5os5qt}fp{17su5tu5os5qt}fq{17su5tu5os5qt}fs{17su5tu5os5qt}ft{17su5tu5ot}7m{5os}fv{17su5tu5ot}fw{17su5tu5ot}}}"),
                        Courier: e("{'widths'{k3w'fof'6o}'kerning'{'fof'-6o}}"),
                        "Courier-BoldOblique": e("{'widths'{k3w'fof'6o}'kerning'{'fof'-6o}}"),
                        "Times-Bold": e("{'widths'{k3q2q5ncx2r201n3m201o6o201s2l201t2l201u2l201w3m201x3m201y3m2k1t2l2l202m2n2n3m2o3m2p6o202q6o2r1w2s2l2t2l2u3m2v3t2w1t2x2l2y1t2z1w3k3m3l3m3m3m3n3m3o3m3p3m3q3m3r3m3s3m203t2l203u2l3v2l3w3t3x3t3y3t3z3m4k5x4l4s4m4m4n4s4o4s4p4m4q3x4r4y4s4y4t2r4u3m4v4y4w4m4x5y4y4s4z4y5k3x5l4y5m4s5n3r5o4m5p4s5q4s5r6o5s4s5t4s5u4m5v2l5w1w5x2l5y3u5z3m6k2l6l3m6m3r6n2w6o3r6p2w6q2l6r3m6s3r6t1w6u2l6v3r6w1w6x5n6y3r6z3m7k3r7l3r7m2w7n2r7o2l7p3r7q3m7r4s7s3m7t3m7u2w7v2r7w1q7x2r7y3o202l3mcl4sal2lam3man3mao3map3mar3mas2lat4uau1yav3maw3tay4uaz2lbk2sbl3t'fof'6obo2lbp3rbr1tbs2lbu2lbv3mbz3mck4s202k3mcm4scn4sco4scp4scq6ocr4scs4mct4mcu4mcv4mcw2r2m3rcy2rcz2rdl4sdm4ydn4ydo4ydp4ydq4yds4ydt4sdu4sdv4sdw4sdz3rek3mel3mem3men3meo3mep3meq4ser2wes2wet2weu2wev2wew1wex1wey1wez1wfl3rfm3mfn3mfo3mfp3mfq3mfr3tfs3mft3rfu3rfv3rfw3rfz3m203k6o212m6o2dw2l2cq2l3t3m3u2l17s4s19m3m}'kerning'{cl{4qt5ks5ot5qy5rw17sv5tv}201t{cks4lscmscnscoscpscls4wv}2k{201ts}2w{4qu5ku7mu5os5qx5ru17su5tu}2x{17su5tu5ou5qs}2y{4qv5kv7mu5ot5qz5ru17su5tu}'fof'-6o7t{cksclscmscnscoscps4ls}3u{17su5tu5os5qu}3v{17su5tu5os5qu}fu{17su5tu5ou5qu}7p{17su5tu5ou5qu}ck{4qt5ks5ot5qy5rw17sv5tv}4l{4qt5ks5ot5qy5rw17sv5tv}cm{4qt5ks5ot5qy5rw17sv5tv}cn{4qt5ks5ot5qy5rw17sv5tv}co{4qt5ks5ot5qy5rw17sv5tv}cp{4qt5ks5ot5qy5rw17sv5tv}6l{17st5tt5ou5qu}17s{ckuclucmucnucoucpu4lu4wu}5o{ckuclucmucnucoucpu4lu4wu}5q{ckzclzcmzcnzcozcpz4lz4wu}5r{ckxclxcmxcnxcoxcpx4lx4wu}5t{ckuclucmucnucoucpu4lu4wu}7q{ckuclucmucnucoucpu4lu}6p{17sw5tw5ou5qu}ek{17st5tt5qu}el{17st5tt5ou5qu}em{17st5tt5qu}en{17st5tt5qu}eo{17st5tt5qu}ep{17st5tt5ou5qu}es{17ss5ts5qu}et{17sw5tw5ou5qu}eu{17sw5tw5ou5qu}ev{17ss5ts5qu}6z{17sw5tw5ou5qu5rs}fm{17sw5tw5ou5qu5rs}fn{17sw5tw5ou5qu5rs}fo{17sw5tw5ou5qu5rs}fp{17sw5tw5ou5qu5rs}fq{17sw5tw5ou5qu5rs}7r{cktcltcmtcntcotcpt4lt5os}fs{17sw5tw5ou5qu5rs}ft{17su5tu5ou5qu}7m{5os}fv{17su5tu5ou5qu}fw{17su5tu5ou5qu}fz{cksclscmscnscoscps4ls}}}"),
                        Helvetica: e("{'widths'{k3p2q4mcx1w201n3r201o6o201s1q201t1q201u1q201w2l201x2l201y2l2k1w2l1w202m2n2n3r2o3r2p5t202q6o2r1n2s2l2t2l2u2r2v3u2w1w2x2l2y1w2z1w3k3r3l3r3m3r3n3r3o3r3p3r3q3r3r3r3s3r203t2l203u2l3v1w3w3u3x3u3y3u3z3r4k6p4l4m4m4m4n4s4o4s4p4m4q3x4r4y4s4s4t1w4u3m4v4m4w3r4x5n4y4s4z4y5k4m5l4y5m4s5n4m5o3x5p4s5q4m5r5y5s4m5t4m5u3x5v1w5w1w5x1w5y2z5z3r6k2l6l3r6m3r6n3m6o3r6p3r6q1w6r3r6s3r6t1q6u1q6v3m6w1q6x5n6y3r6z3r7k3r7l3r7m2l7n3m7o1w7p3r7q3m7r4s7s3m7t3m7u3m7v2l7w1u7x2l7y3u202l3rcl4mal2lam3ran3rao3rap3rar3ras2lat4tau2pav3raw3uay4taz2lbk2sbl3u'fof'6obo2lbp3rbr1wbs2lbu2obv3rbz3xck4m202k3rcm4mcn4mco4mcp4mcq6ocr4scs4mct4mcu4mcv4mcw1w2m2ncy1wcz1wdl4sdm4ydn4ydo4ydp4ydq4yds4ydt4sdu4sdv4sdw4sdz3xek3rel3rem3ren3reo3rep3req5ter3mes3ret3reu3rev3rew1wex1wey1wez1wfl3rfm3rfn3rfo3rfp3rfq3rfr3ufs3xft3rfu3rfv3rfw3rfz3m203k6o212m6o2dw2l2cq2l3t3r3u1w17s4m19m3r}'kerning'{5q{4wv}cl{4qs5kw5ow5qs17sv5tv}201t{2wu4w1k2yu}201x{2wu4wy2yu}17s{2ktclucmucnu4otcpu4lu4wycoucku}2w{7qs4qz5k1m17sy5ow5qx5rsfsu5ty7tufzu}2x{17sy5ty5oy5qs}2y{7qs4qz5k1m17sy5ow5qx5rsfsu5ty7tufzu}'fof'-6o7p{17sv5tv5ow}ck{4qs5kw5ow5qs17sv5tv}4l{4qs5kw5ow5qs17sv5tv}cm{4qs5kw5ow5qs17sv5tv}cn{4qs5kw5ow5qs17sv5tv}co{4qs5kw5ow5qs17sv5tv}cp{4qs5kw5ow5qs17sv5tv}6l{17sy5ty5ow}do{17st5tt}4z{17st5tt}7s{fst}dm{17st5tt}dn{17st5tt}5o{ckwclwcmwcnwcowcpw4lw4wv}dp{17st5tt}dq{17st5tt}7t{5ow}ds{17st5tt}5t{2ktclucmucnu4otcpu4lu4wycoucku}fu{17sv5tv5ow}6p{17sy5ty5ow5qs}ek{17sy5ty5ow}el{17sy5ty5ow}em{17sy5ty5ow}en{5ty}eo{17sy5ty5ow}ep{17sy5ty5ow}es{17sy5ty5qs}et{17sy5ty5ow5qs}eu{17sy5ty5ow5qs}ev{17sy5ty5ow5qs}6z{17sy5ty5ow5qs}fm{17sy5ty5ow5qs}fn{17sy5ty5ow5qs}fo{17sy5ty5ow5qs}fp{17sy5ty5qs}fq{17sy5ty5ow5qs}7r{5ow}fs{17sy5ty5ow5qs}ft{17sv5tv5ow}7m{5ow}fv{17sv5tv5ow}fw{17sv5tv5ow}}}"),
                        "Helvetica-BoldOblique": e("{'widths'{k3s2q4scx1w201n3r201o6o201s1w201t1w201u1w201w3m201x3m201y3m2k1w2l2l202m2n2n3r2o3r2p5t202q6o2r1s2s2l2t2l2u2r2v3u2w1w2x2l2y1w2z1w3k3r3l3r3m3r3n3r3o3r3p3r3q3r3r3r3s3r203t2l203u2l3v2l3w3u3x3u3y3u3z3x4k6l4l4s4m4s4n4s4o4s4p4m4q3x4r4y4s4s4t1w4u3r4v4s4w3x4x5n4y4s4z4y5k4m5l4y5m4s5n4m5o3x5p4s5q4m5r5y5s4m5t4m5u3x5v2l5w1w5x2l5y3u5z3r6k2l6l3r6m3x6n3r6o3x6p3r6q2l6r3x6s3x6t1w6u1w6v3r6w1w6x5t6y3x6z3x7k3x7l3x7m2r7n3r7o2l7p3x7q3r7r4y7s3r7t3r7u3m7v2r7w1w7x2r7y3u202l3rcl4sal2lam3ran3rao3rap3rar3ras2lat4tau2pav3raw3uay4taz2lbk2sbl3u'fof'6obo2lbp3xbq3rbr1wbs2lbu2obv3rbz3xck4s202k3rcm4scn4sco4scp4scq6ocr4scs4mct4mcu4mcv4mcw1w2m2zcy1wcz1wdl4sdm4ydn4ydo4ydp4ydq4yds4ydt4sdu4sdv4sdw4sdz3xek3rel3rem3ren3reo3rep3req5ter3res3ret3reu3rev3rew1wex1wey1wez1wfl3xfm3xfn3xfo3xfp3xfq3xfr3ufs3xft3xfu3xfv3xfw3xfz3r203k6o212m6o2dw2l2cq2l3t3r3u2l17s4m19m3r}'kerning'{cl{4qs5ku5ot5qs17sv5tv}201t{2ww4wy2yw}201w{2ks}201x{2ww4wy2yw}2k{201ts201xs}2w{7qs4qu5kw5os5qw5rs17su5tu7tsfzs}2x{5ow5qs}2y{7qs4qu5kw5os5qw5rs17su5tu7tsfzs}'fof'-6o7p{17su5tu5ot}ck{4qs5ku5ot5qs17sv5tv}4l{4qs5ku5ot5qs17sv5tv}cm{4qs5ku5ot5qs17sv5tv}cn{4qs5ku5ot5qs17sv5tv}co{4qs5ku5ot5qs17sv5tv}cp{4qs5ku5ot5qs17sv5tv}6l{17st5tt5os}17s{2kwclvcmvcnvcovcpv4lv4wwckv}5o{2kucltcmtcntcotcpt4lt4wtckt}5q{2ksclscmscnscoscps4ls4wvcks}5r{2ks4ws}5t{2kwclvcmvcnvcovcpv4lv4wwckv}eo{17st5tt5os}fu{17su5tu5ot}6p{17ss5ts}ek{17st5tt5os}el{17st5tt5os}em{17st5tt5os}en{17st5tt5os}6o{201ts}ep{17st5tt5os}es{17ss5ts}et{17ss5ts}eu{17ss5ts}ev{17ss5ts}6z{17su5tu5os5qt}fm{17su5tu5os5qt}fn{17su5tu5os5qt}fo{17su5tu5os5qt}fp{17su5tu5os5qt}fq{17su5tu5os5qt}fs{17su5tu5os5qt}ft{17su5tu5ot}7m{5os}fv{17su5tu5ot}fw{17su5tu5ot}}}"),
                        "Courier-Bold": e("{'widths'{k3w'fof'6o}'kerning'{'fof'-6o}}"),
                        "Times-Italic": e("{'widths'{k3n2q4ycx2l201n3m201o5t201s2l201t2l201u2l201w3r201x3r201y3r2k1t2l2l202m2n2n3m2o3m2p5n202q5t2r1p2s2l2t2l2u3m2v4n2w1t2x2l2y1t2z1w3k3m3l3m3m3m3n3m3o3m3p3m3q3m3r3m3s3m203t2l203u2l3v2l3w4n3x4n3y4n3z3m4k5w4l3x4m3x4n4m4o4s4p3x4q3x4r4s4s4s4t2l4u2w4v4m4w3r4x5n4y4m4z4s5k3x5l4s5m3x5n3m5o3r5p4s5q3x5r5n5s3x5t3r5u3r5v2r5w1w5x2r5y2u5z3m6k2l6l3m6m3m6n2w6o3m6p2w6q1w6r3m6s3m6t1w6u1w6v2w6w1w6x4s6y3m6z3m7k3m7l3m7m2r7n2r7o1w7p3m7q2w7r4m7s2w7t2w7u2r7v2s7w1v7x2s7y3q202l3mcl3xal2ram3man3mao3map3mar3mas2lat4wau1vav3maw4nay4waz2lbk2sbl4n'fof'6obo2lbp3mbq3obr1tbs2lbu1zbv3mbz3mck3x202k3mcm3xcn3xco3xcp3xcq5tcr4mcs3xct3xcu3xcv3xcw2l2m2ucy2lcz2ldl4mdm4sdn4sdo4sdp4sdq4sds4sdt4sdu4sdv4sdw4sdz3mek3mel3mem3men3meo3mep3meq4mer2wes2wet2weu2wev2wew1wex1wey1wez1wfl3mfm3mfn3mfo3mfp3mfq3mfr4nfs3mft3mfu3mfv3mfw3mfz2w203k6o212m6m2dw2l2cq2l3t3m3u2l17s3r19m3m}'kerning'{cl{5kt4qw}201s{201sw}201t{201tw2wy2yy6q-t}201x{2wy2yy}2k{201tw}2w{7qs4qy7rs5ky7mw5os5qx5ru17su5tu}2x{17ss5ts5os}2y{7qs4qy7rs5ky7mw5os5qx5ru17su5tu}'fof'-6o6t{17ss5ts5qs}7t{5os}3v{5qs}7p{17su5tu5qs}ck{5kt4qw}4l{5kt4qw}cm{5kt4qw}cn{5kt4qw}co{5kt4qw}cp{5kt4qw}6l{4qs5ks5ou5qw5ru17su5tu}17s{2ks}5q{ckvclvcmvcnvcovcpv4lv}5r{ckuclucmucnucoucpu4lu}5t{2ks}6p{4qs5ks5ou5qw5ru17su5tu}ek{4qs5ks5ou5qw5ru17su5tu}el{4qs5ks5ou5qw5ru17su5tu}em{4qs5ks5ou5qw5ru17su5tu}en{4qs5ks5ou5qw5ru17su5tu}eo{4qs5ks5ou5qw5ru17su5tu}ep{4qs5ks5ou5qw5ru17su5tu}es{5ks5qs4qs}et{4qs5ks5ou5qw5ru17su5tu}eu{4qs5ks5qw5ru17su5tu}ev{5ks5qs4qs}ex{17ss5ts5qs}6z{4qv5ks5ou5qw5ru17su5tu}fm{4qv5ks5ou5qw5ru17su5tu}fn{4qv5ks5ou5qw5ru17su5tu}fo{4qv5ks5ou5qw5ru17su5tu}fp{4qv5ks5ou5qw5ru17su5tu}fq{4qv5ks5ou5qw5ru17su5tu}7r{5os}fs{4qv5ks5ou5qw5ru17su5tu}ft{17su5tu5qs}fu{17su5tu5qs}fv{17su5tu5qs}fw{17su5tu5qs}}}"),
                        "Times-Roman": e("{'widths'{k3n2q4ycx2l201n3m201o6o201s2l201t2l201u2l201w2w201x2w201y2w2k1t2l2l202m2n2n3m2o3m2p5n202q6o2r1m2s2l2t2l2u3m2v3s2w1t2x2l2y1t2z1w3k3m3l3m3m3m3n3m3o3m3p3m3q3m3r3m3s3m203t2l203u2l3v1w3w3s3x3s3y3s3z2w4k5w4l4s4m4m4n4m4o4s4p3x4q3r4r4s4s4s4t2l4u2r4v4s4w3x4x5t4y4s4z4s5k3r5l4s5m4m5n3r5o3x5p4s5q4s5r5y5s4s5t4s5u3x5v2l5w1w5x2l5y2z5z3m6k2l6l2w6m3m6n2w6o3m6p2w6q2l6r3m6s3m6t1w6u1w6v3m6w1w6x4y6y3m6z3m7k3m7l3m7m2l7n2r7o1w7p3m7q3m7r4s7s3m7t3m7u2w7v3k7w1o7x3k7y3q202l3mcl4sal2lam3man3mao3map3mar3mas2lat4wau1vav3maw3say4waz2lbk2sbl3s'fof'6obo2lbp3mbq2xbr1tbs2lbu1zbv3mbz2wck4s202k3mcm4scn4sco4scp4scq5tcr4mcs3xct3xcu3xcv3xcw2l2m2tcy2lcz2ldl4sdm4sdn4sdo4sdp4sdq4sds4sdt4sdu4sdv4sdw4sdz3mek2wel2wem2wen2weo2wep2weq4mer2wes2wet2weu2wev2wew1wex1wey1wez1wfl3mfm3mfn3mfo3mfp3mfq3mfr3sfs3mft3mfu3mfv3mfw3mfz3m203k6o212m6m2dw2l2cq2l3t3m3u1w17s4s19m3m}'kerning'{cl{4qs5ku17sw5ou5qy5rw201ss5tw201ws}201s{201ss}201t{ckw4lwcmwcnwcowcpwclw4wu201ts}2k{201ts}2w{4qs5kw5os5qx5ru17sx5tx}2x{17sw5tw5ou5qu}2y{4qs5kw5os5qx5ru17sx5tx}'fof'-6o7t{ckuclucmucnucoucpu4lu5os5rs}3u{17su5tu5qs}3v{17su5tu5qs}7p{17sw5tw5qs}ck{4qs5ku17sw5ou5qy5rw201ss5tw201ws}4l{4qs5ku17sw5ou5qy5rw201ss5tw201ws}cm{4qs5ku17sw5ou5qy5rw201ss5tw201ws}cn{4qs5ku17sw5ou5qy5rw201ss5tw201ws}co{4qs5ku17sw5ou5qy5rw201ss5tw201ws}cp{4qs5ku17sw5ou5qy5rw201ss5tw201ws}6l{17su5tu5os5qw5rs}17s{2ktclvcmvcnvcovcpv4lv4wuckv}5o{ckwclwcmwcnwcowcpw4lw4wu}5q{ckyclycmycnycoycpy4ly4wu5ms}5r{cktcltcmtcntcotcpt4lt4ws}5t{2ktclvcmvcnvcovcpv4lv4wuckv}7q{cksclscmscnscoscps4ls}6p{17su5tu5qw5rs}ek{5qs5rs}el{17su5tu5os5qw5rs}em{17su5tu5os5qs5rs}en{17su5qs5rs}eo{5qs5rs}ep{17su5tu5os5qw5rs}es{5qs}et{17su5tu5qw5rs}eu{17su5tu5qs5rs}ev{5qs}6z{17sv5tv5os5qx5rs}fm{5os5qt5rs}fn{17sv5tv5os5qx5rs}fo{17sv5tv5os5qx5rs}fp{5os5qt5rs}fq{5os5qt5rs}7r{ckuclucmucnucoucpu4lu5os}fs{17sv5tv5os5qx5rs}ft{17ss5ts5qs}fu{17sw5tw5qs}fv{17sw5tw5qs}fw{17ss5ts5qs}fz{ckuclucmucnucoucpu4lu5os5rs}}}"),
                        "Helvetica-Oblique": e("{'widths'{k3p2q4mcx1w201n3r201o6o201s1q201t1q201u1q201w2l201x2l201y2l2k1w2l1w202m2n2n3r2o3r2p5t202q6o2r1n2s2l2t2l2u2r2v3u2w1w2x2l2y1w2z1w3k3r3l3r3m3r3n3r3o3r3p3r3q3r3r3r3s3r203t2l203u2l3v1w3w3u3x3u3y3u3z3r4k6p4l4m4m4m4n4s4o4s4p4m4q3x4r4y4s4s4t1w4u3m4v4m4w3r4x5n4y4s4z4y5k4m5l4y5m4s5n4m5o3x5p4s5q4m5r5y5s4m5t4m5u3x5v1w5w1w5x1w5y2z5z3r6k2l6l3r6m3r6n3m6o3r6p3r6q1w6r3r6s3r6t1q6u1q6v3m6w1q6x5n6y3r6z3r7k3r7l3r7m2l7n3m7o1w7p3r7q3m7r4s7s3m7t3m7u3m7v2l7w1u7x2l7y3u202l3rcl4mal2lam3ran3rao3rap3rar3ras2lat4tau2pav3raw3uay4taz2lbk2sbl3u'fof'6obo2lbp3rbr1wbs2lbu2obv3rbz3xck4m202k3rcm4mcn4mco4mcp4mcq6ocr4scs4mct4mcu4mcv4mcw1w2m2ncy1wcz1wdl4sdm4ydn4ydo4ydp4ydq4yds4ydt4sdu4sdv4sdw4sdz3xek3rel3rem3ren3reo3rep3req5ter3mes3ret3reu3rev3rew1wex1wey1wez1wfl3rfm3rfn3rfo3rfp3rfq3rfr3ufs3xft3rfu3rfv3rfw3rfz3m203k6o212m6o2dw2l2cq2l3t3r3u1w17s4m19m3r}'kerning'{5q{4wv}cl{4qs5kw5ow5qs17sv5tv}201t{2wu4w1k2yu}201x{2wu4wy2yu}17s{2ktclucmucnu4otcpu4lu4wycoucku}2w{7qs4qz5k1m17sy5ow5qx5rsfsu5ty7tufzu}2x{17sy5ty5oy5qs}2y{7qs4qz5k1m17sy5ow5qx5rsfsu5ty7tufzu}'fof'-6o7p{17sv5tv5ow}ck{4qs5kw5ow5qs17sv5tv}4l{4qs5kw5ow5qs17sv5tv}cm{4qs5kw5ow5qs17sv5tv}cn{4qs5kw5ow5qs17sv5tv}co{4qs5kw5ow5qs17sv5tv}cp{4qs5kw5ow5qs17sv5tv}6l{17sy5ty5ow}do{17st5tt}4z{17st5tt}7s{fst}dm{17st5tt}dn{17st5tt}5o{ckwclwcmwcnwcowcpw4lw4wv}dp{17st5tt}dq{17st5tt}7t{5ow}ds{17st5tt}5t{2ktclucmucnu4otcpu4lu4wycoucku}fu{17sv5tv5ow}6p{17sy5ty5ow5qs}ek{17sy5ty5ow}el{17sy5ty5ow}em{17sy5ty5ow}en{5ty}eo{17sy5ty5ow}ep{17sy5ty5ow}es{17sy5ty5qs}et{17sy5ty5ow5qs}eu{17sy5ty5ow5qs}ev{17sy5ty5ow5qs}6z{17sy5ty5ow5qs}fm{17sy5ty5ow5qs}fn{17sy5ty5ow5qs}fo{17sy5ty5ow5qs}fp{17sy5ty5qs}fq{17sy5ty5ow5qs}7r{5ow}fs{17sy5ty5ow5qs}ft{17sv5tv5ow}7m{5ow}fv{17sv5tv5ow}fw{17sv5tv5ow}}}")
                    }
                };
            t.events.push(["addFont", function(t) {
                var e, n, o;
                (e = i.Unicode[t.PostScriptName]) && ((n = t.metadata.Unicode ? t.metadata.Unicode : t.metadata.Unicode = {}).widths = e.widths, n.kerning = e.kerning), (o = r.Unicode[t.PostScriptName]) && ((n = t.metadata.Unicode ? t.metadata.Unicode : t.metadata.Unicode = {}).encoding = o, o.codePages && o.codePages.length && (t.encoding = o.codePages[0]))
            }])
        }(e.API), e.API.addSVG = function(t, e, n, r, i) {
            function o(t, e) {
                var n = e.createElement("style");
                n.type = "text/css", n.styleSheet ? n.styleSheet.cssText = t : n.appendChild(e.createTextNode(t)), e.getElementsByTagName("head")[0].appendChild(n)
            }
            if (void 0 === e || void 0 === n) throw new Error("addSVG needs values for 'x' and 'y'");
            var a = function(t, e) {
                    var n = (e.contentWindow || e.contentDocument).document;
                    return n.write(t), n.close(), n.getElementsByTagName("svg")[0]
                }(t, function(t) {
                    var e = t.createElement("iframe");
                    return o(".jsPDF_sillysvg_iframe {display:none;position:absolute;}", t), e.name = "childframe", e.setAttribute("width", 0), e.setAttribute("height", 0), e.setAttribute("frameborder", "0"), e.setAttribute("scrolling", "no"), e.setAttribute("seamless", "seamless"), e.setAttribute("class", "jsPDF_sillysvg_iframe"), t.body.appendChild(e), e
                }(document)),
                s = [1, 1],
                c = parseFloat(a.getAttribute("width")),
                l = parseFloat(a.getAttribute("height"));
            c && l && (r && i ? s = [r / c, i / l] : r ? s = [r / c, r / c] : i && (s = [i / l, i / l]));
            var u, h, f, d, p = a.childNodes;
            for (u = 0, h = p.length; u < h; u++)(f = p[u]).tagName && "PATH" === f.tagName.toUpperCase() && ((d = function(t) {
                for (var e = parseFloat(t[1]), n = parseFloat(t[2]), r = [], i = 3, o = t.length; i < o;) "c" === t[i] ? (r.push([parseFloat(t[i + 1]), parseFloat(t[i + 2]), parseFloat(t[i + 3]), parseFloat(t[i + 4]), parseFloat(t[i + 5]), parseFloat(t[i + 6])]), i += 7) : "l" === t[i] ? (r.push([parseFloat(t[i + 1]), parseFloat(t[i + 2])]), i += 3) : i += 1;
                return [e, n, r]
            }(f.getAttribute("d").split(" ")))[0] = d[0] * s[0] + e, d[1] = d[1] * s[1] + n, this.lines.call(this, d[2], d[0], d[1], s));
            return this
        }, e.API.putTotalPages = function(t) {
            for (var e = new RegExp(t, "g"), n = 1; n <= this.internal.getNumberOfPages(); n++)
                for (var r = 0; r < this.internal.pages[n].length; r++) this.internal.pages[n][r] = this.internal.pages[n][r].replace(e, this.internal.getNumberOfPages());
            return this
        }, e.API.viewerPreferences = function(e, n) {
            function r(t, e) {
                var n, r = !1;
                for (n = 0; n < t.length; n += 1) t[n] === e && (r = !0);
                return r
            }
            e = e || {}, n = n || !1;
            var i, o, a, s = {
                    HideToolbar: {
                        defaultValue: !1,
                        value: !1,
                        type: "boolean",
                        explicitSet: !1,
                        valueSet: [!0, !1],
                        pdfVersion: 1.3
                    },
                    HideMenubar: {
                        defaultValue: !1,
                        value: !1,
                        type: "boolean",
                        explicitSet: !1,
                        valueSet: [!0, !1],
                        pdfVersion: 1.3
                    },
                    HideWindowUI: {
                        defaultValue: !1,
                        value: !1,
                        type: "boolean",
                        explicitSet: !1,
                        valueSet: [!0, !1],
                        pdfVersion: 1.3
                    },
                    FitWindow: {
                        defaultValue: !1,
                        value: !1,
                        type: "boolean",
                        explicitSet: !1,
                        valueSet: [!0, !1],
                        pdfVersion: 1.3
                    },
                    CenterWindow: {
                        defaultValue: !1,
                        value: !1,
                        type: "boolean",
                        explicitSet: !1,
                        valueSet: [!0, !1],
                        pdfVersion: 1.3
                    },
                    DisplayDocTitle: {
                        defaultValue: !1,
                        value: !1,
                        type: "boolean",
                        explicitSet: !1,
                        valueSet: [!0, !1],
                        pdfVersion: 1.4
                    },
                    NonFullScreenPageMode: {
                        defaultValue: "UseNone",
                        value: "UseNone",
                        type: "name",
                        explicitSet: !1,
                        valueSet: ["UseNone", "UseOutlines", "UseThumbs", "UseOC"],
                        pdfVersion: 1.3
                    },
                    Direction: {
                        defaultValue: "L2R",
                        value: "L2R",
                        type: "name",
                        explicitSet: !1,
                        valueSet: ["L2R", "R2L"],
                        pdfVersion: 1.3
                    },
                    ViewArea: {
                        defaultValue: "CropBox",
                        value: "CropBox",
                        type: "name",
                        explicitSet: !1,
                        valueSet: ["MediaBox", "CropBox", "TrimBox", "BleedBox", "ArtBox"],
                        pdfVersion: 1.4
                    },
                    ViewClip: {
                        defaultValue: "CropBox",
                        value: "CropBox",
                        type: "name",
                        explicitSet: !1,
                        valueSet: ["MediaBox", "CropBox", "TrimBox", "BleedBox", "ArtBox"],
                        pdfVersion: 1.4
                    },
                    PrintArea: {
                        defaultValue: "CropBox",
                        value: "CropBox",
                        type: "name",
                        explicitSet: !1,
                        valueSet: ["MediaBox", "CropBox", "TrimBox", "BleedBox", "ArtBox"],
                        pdfVersion: 1.4
                    },
                    PrintClip: {
                        defaultValue: "CropBox",
                        value: "CropBox",
                        type: "name",
                        explicitSet: !1,
                        valueSet: ["MediaBox", "CropBox", "TrimBox", "BleedBox", "ArtBox"],
                        pdfVersion: 1.4
                    },
                    PrintScaling: {
                        defaultValue: "AppDefault",
                        value: "AppDefault",
                        type: "name",
                        explicitSet: !1,
                        valueSet: ["AppDefault", "None"],
                        pdfVersion: 1.6
                    },
                    Duplex: {
                        defaultValue: "",
                        value: "none",
                        type: "name",
                        explicitSet: !1,
                        valueSet: ["Simplex", "DuplexFlipShortEdge", "DuplexFlipLongEdge", "none"],
                        pdfVersion: 1.7
                    },
                    PickTrayByPDFSize: {
                        defaultValue: !1,
                        value: !1,
                        type: "boolean",
                        explicitSet: !1,
                        valueSet: [!0, !1],
                        pdfVersion: 1.7
                    },
                    PrintPageRange: {
                        defaultValue: "",
                        value: "",
                        type: "array",
                        explicitSet: !1,
                        valueSet: null,
                        pdfVersion: 1.7
                    },
                    NumCopies: {
                        defaultValue: 1,
                        value: 1,
                        type: "integer",
                        explicitSet: !1,
                        valueSet: null,
                        pdfVersion: 1.7
                    }
                },
                c = Object.keys(s),
                l = [],
                u = 0,
                h = 0,
                f = 0,
                d = !0;
            if (void 0 === this.internal.viewerpreferences && (this.internal.viewerpreferences = {}, this.internal.viewerpreferences.configuration = JSON.parse(JSON.stringify(s)), this.internal.viewerpreferences.isSubscribed = !1), i = this.internal.viewerpreferences.configuration, "reset" === e || !0 === n) {
                var p = c.length;
                for (f = 0; f < p; f += 1) i[c[f]].value = i[c[f]].defaultValue, i[c[f]].explicitSet = !1
            }
            if ("object" === (void 0 === e ? "undefined" : t(e)))
                for (o in e)
                    if (a = e[o], r(c, o) && void 0 !== a) {
                        if ("boolean" === i[o].type && "boolean" == typeof a) i[o].value = a;
                        else if ("name" === i[o].type && r(i[o].valueSet, a)) i[o].value = a;
                        else if ("integer" === i[o].type && Number.isInteger(a)) i[o].value = a;
                        else if ("array" === i[o].type) {
                            for (u = 0; u < a.length; u += 1)
                                if (d = !0, 1 === a[u].length && "number" == typeof a[u][0]) l.push(String(a[u]));
                                else if (a[u].length > 1) {
                                for (h = 0; h < a[u].length; h += 1) "number" != typeof a[u][h] && (d = !1);
                                !0 === d && l.push(String(a[u].join("-")))
                            }
                            i[o].value = String(l)
                        } else i[o].value = i[o].defaultValue;
                        i[o].explicitSet = !0
                    }
            return !1 === this.internal.viewerpreferences.isSubscribed && (this.internal.events.subscribe("putCatalog", function() {
                var t, e = [];
                for (t in i) !0 === i[t].explicitSet && ("name" === i[t].type ? e.push("/" + t + " /" + i[t].value) : e.push("/" + t + " " + i[t].value));
                0 !== e.length && this.internal.write("/ViewerPreferences<<\n" + e.join("\n") + "\n>>")
            }), this.internal.viewerpreferences.isSubscribed = !0), this.internal.viewerpreferences.configuration = i, this
        },
        /** ==================================================================== 
         * jsPDF XMP metadata plugin
         * Copyright (c) 2016 Jussi Utunen, u-jussi@suomi24.fi
         * 
         * 
         * ====================================================================
         */
        function(t) {
            var n = "",
                r = "",
                i = "";
            e.API.addMetadata = function(t, e) {
                return r = e || "http://jspdf.default.namespaceuri/", n = t, this.internal.events.subscribe("postPutResources", function() {
                    if (n) {
                        var t = '<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"><rdf:Description rdf:about="" xmlns:jspdf="' + r + '"><jspdf:metadata>',
                            e = unescape(encodeURIComponent('<x:xmpmeta xmlns:x="adobe:ns:meta/">')),
                            o = unescape(encodeURIComponent(t)),
                            a = unescape(encodeURIComponent(n)),
                            s = unescape(encodeURIComponent("</jspdf:metadata></rdf:Description></rdf:RDF>")),
                            c = unescape(encodeURIComponent("</x:xmpmeta>")),
                            l = o.length + a.length + s.length + e.length + c.length;
                        i = this.internal.newObject(), this.internal.write("<< /Type /Metadata /Subtype /XML /Length " + l + " >>"), this.internal.write("stream"), this.internal.write(e + o + a + s + c), this.internal.write("endstream"), this.internal.write("endobj")
                    } else i = ""
                }), this.internal.events.subscribe("putCatalog", function() {
                    i && this.internal.write("/Metadata " + i + " 0 R")
                }), this
            }
        }(),
        function(t) {
            if (t.URL = t.URL || t.webkitURL, t.Blob && t.URL) try {
                return void new Blob
            } catch (t) {}
            var e = t.BlobBuilder || t.WebKitBlobBuilder || t.MozBlobBuilder || function(t) {
                var e = function(t) {
                        return Object.prototype.toString.call(t).match(/^\[object\s(.*)\]$/)[1]
                    },
                    n = function() {
                        this.data = []
                    },
                    r = function(t, e, n) {
                        this.data = t, this.size = t.length, this.type = e, this.encoding = n
                    },
                    i = n.prototype,
                    o = r.prototype,
                    a = t.FileReaderSync,
                    s = function(t) {
                        this.code = this[this.name = t]
                    },
                    c = "NOT_FOUND_ERR SECURITY_ERR ABORT_ERR NOT_READABLE_ERR ENCODING_ERR NO_MODIFICATION_ALLOWED_ERR INVALID_STATE_ERR SYNTAX_ERR".split(" "),
                    l = c.length,
                    u = t.URL || t.webkitURL || t,
                    h = u.createObjectURL,
                    f = u.revokeObjectURL,
                    d = u,
                    p = t.btoa,
                    m = t.atob,
                    g = t.ArrayBuffer,
                    w = t.Uint8Array,
                    y = /^[\w-]+:\/*\[?[\w\.:-]+\]?(?::[0-9]+)?/;
                for (r.fake = o.fake = !0; l--;) s.prototype[c[l]] = l + 1;
                return u.createObjectURL || (d = t.URL = function(t) {
                    var e, n = document.createElementNS("http://www.w3.org/1999/xhtml", "a");
                    return n.href = t, "origin" in n || ("data:" === n.protocol.toLowerCase() ? n.origin = null : (e = t.match(y), n.origin = e && e[1])), n
                }), d.createObjectURL = function(t) {
                    var e, n = t.type;
                    return null === n && (n = "application/octet-stream"), t instanceof r ? (e = "data:" + n, "base64" === t.encoding ? e + ";base64," + t.data : "URI" === t.encoding ? e + "," + decodeURIComponent(t.data) : p ? e + ";base64," + p(t.data) : e + "," + encodeURIComponent(t.data)) : h ? h.call(u, t) : void 0
                }, d.revokeObjectURL = function(t) {
                    "data:" !== t.substring(0, 5) && f && f.call(u, t)
                }, i.append = function(t) {
                    var n = this.data;
                    if (w && (t instanceof g || t instanceof w)) {
                        for (var i = "", o = new w(t), c = 0, l = o.length; c < l; c++) i += String.fromCharCode(o[c]);
                        n.push(i)
                    } else if ("Blob" === e(t) || "File" === e(t)) {
                        if (!a) throw new s("NOT_READABLE_ERR");
                        var u = new a;
                        n.push(u.readAsBinaryString(t))
                    } else t instanceof r ? "base64" === t.encoding && m ? n.push(m(t.data)) : "URI" === t.encoding ? n.push(decodeURIComponent(t.data)) : "raw" === t.encoding && n.push(t.data) : ("string" != typeof t && (t += ""), n.push(unescape(encodeURIComponent(t))))
                }, i.getBlob = function(t) {
                    return arguments.length || (t = null), new r(this.data.join(""), t, "raw")
                }, i.toString = function() {
                    return "[object BlobBuilder]"
                }, o.slice = function(t, e, n) {
                    var i = arguments.length;
                    return i < 3 && (n = null), new r(this.data.slice(t, i > 1 ? e : this.data.length), n, this.encoding)
                }, o.toString = function() {
                    return "[object Blob]"
                }, o.close = function() {
                    this.size = 0, delete this.data
                }, n
            }(t);
            t.Blob = function(t, n) {
                var r = n ? n.type || "" : "",
                    i = new e;
                if (t)
                    for (var o = 0, a = t.length; o < a; o++) Uint8Array && t[o] instanceof Uint8Array ? i.append(t[o].buffer) : i.append(t[o]);
                var s = i.getBlob(r);
                return !s.slice && s.webkitSlice && (s.slice = s.webkitSlice), s
            };
            var n = Object.getPrototypeOf || function(t) {
                return t.__proto__
            };
            t.Blob.prototype = n(new t.Blob)
        }("undefined" != typeof self && self || "undefined" != typeof window && window || (void 0).content || void 0);
    var r = r || function(t) {
        if (!(void 0 === t || "undefined" != typeof navigator && /MSIE [1-9]\./.test(navigator.userAgent))) {
            var e = function() {
                    return t.URL || t.webkitURL || t
                },
                n = t.document.createElementNS("http://www.w3.org/1999/xhtml", "a"),
                r = "download" in n,
                i = function(t) {
                    var e = new MouseEvent("click");
                    t.dispatchEvent(e)
                },
                o = /constructor/i.test(t.HTMLElement) || t.safari,
                a = /CriOS\/[\d]+/.test(navigator.userAgent),
                s = function(e) {
                    (t.setImmediate || t.setTimeout)(function() {
                        throw e
                    }, 0)
                },
                c = function(t) {
                    setTimeout(function() {
                        "string" == typeof t ? e().revokeObjectURL(t) : t.remove()
                    }, 4e4)
                },
                l = function(t, e, n) {
                    for (var r = (e = [].concat(e)).length; r--;) {
                        var i = t["on" + e[r]];
                        if ("function" == typeof i) try {
                            i.call(t, n || t)
                        } catch (t) {
                            s(t)
                        }
                    }
                },
                u = function(t) {
                    return /^\s*(?:text\/\S*|application\/xml|\S*\/\S*\+xml)\s*;.*charset\s*=\s*utf-8/i.test(t.type) ? new Blob([String.fromCharCode(65279), t], {
                        type: t.type
                    }) : t
                },
                h = function(s, h, f) {
                    f || (s = u(s));
                    var d, p = this,
                        m = "application/octet-stream" === s.type,
                        g = function() {
                            l(p, "writestart progress write writeend".split(" "))
                        };
                    if (p.readyState = p.INIT, r) return d = e().createObjectURL(s), void setTimeout(function() {
                        n.href = d, n.download = h, i(n), g(), c(d), p.readyState = p.DONE
                    });
                    ! function() {
                        if ((a || m && o) && t.FileReader) {
                            var n = new FileReader;
                            return n.onloadend = function() {
                                var e = a ? n.result : n.result.replace(/^data:[^;]*;/, "data:attachment/file;");
                                t.open(e, "_blank") || (t.location.href = e), e = void 0, p.readyState = p.DONE, g()
                            }, n.readAsDataURL(s), void(p.readyState = p.INIT)
                        }
                        d || (d = e().createObjectURL(s)), m ? t.location.href = d : t.open(d, "_blank") || (t.location.href = d), p.readyState = p.DONE, g(), c(d)
                    }()
                },
                f = h.prototype;
            return "undefined" != typeof navigator && navigator.msSaveOrOpenBlob ? function(t, e, n) {
                return e = e || t.name || "download", n || (t = u(t)), navigator.msSaveOrOpenBlob(t, e)
            } : (f.abort = function() {}, f.readyState = f.INIT = 0, f.WRITING = 1, f.DONE = 2, f.error = f.onwritestart = f.onprogress = f.onwrite = f.onabort = f.onerror = f.onwriteend = null, function(t, e, n) {
                return new h(t, e || t.name || "download", n)
            })
        }
    }("undefined" != typeof self && self || "undefined" != typeof window && window || (void 0).content);
    "undefined" != typeof module && module.exports ? module.exports.saveAs = r : "undefined" != typeof define && null !== define && null !== define.amd && define("FileSaver.js", function() {
            return r
        }),
        function(t, e) {
            "object" == typeof module ? module.exports = e() : "function" == typeof define ? define("adler32cs", [], e) : t.adler32cs = e()
        }(e, function() {
            var t = "function" == typeof ArrayBuffer && "function" == typeof Uint8Array,
                e = null,
                n = function() {
                    if (!t) return function() {
                        return !1
                    };
                    try {
                        var n = {};
                        "function" == typeof n.Buffer && (e = n.Buffer)
                    } catch (t) {}
                    return function(t) {
                        return t instanceof ArrayBuffer || null !== e && t instanceof e
                    }
                }(),
                r = null !== e ? function(t) {
                    return new e(t, "utf8").toString("binary")
                } : function(t) {
                    return unescape(encodeURIComponent(t))
                },
                i = function(t, e) {
                    for (var n = 65535 & t, r = t >>> 16, i = 0, o = e.length; i < o; i++) r = (r + (n = (n + (255 & e.charCodeAt(i))) % 65521)) % 65521;
                    return (r << 16 | n) >>> 0
                },
                o = function(t, e) {
                    for (var n = 65535 & t, r = t >>> 16, i = 0, o = e.length; i < o; i++) r = (r + (n = (n + e[i]) % 65521)) % 65521;
                    return (r << 16 | n) >>> 0
                },
                a = {},
                s = a.Adler32 = function() {
                    var e = function(t) {
                            if (!(this instanceof e)) throw new TypeError("Constructor cannot called be as a function.");
                            if (!isFinite(t = null == t ? 1 : +t)) throw new Error("First arguments needs to be a finite number.");
                            this.checksum = t >>> 0
                        },
                        a = e.prototype = {};
                    return a.constructor = e, e.from = function(t) {
                        return t.prototype = a, t
                    }(function(t) {
                        if (!(this instanceof e)) throw new TypeError("Constructor cannot called be as a function.");
                        if (null == t) throw new Error("First argument needs to be a string.");
                        this.checksum = i(1, t.toString())
                    }), e.fromUtf8 = function(t) {
                        return t.prototype = a, t
                    }(function(t) {
                        if (!(this instanceof e)) throw new TypeError("Constructor cannot called be as a function.");
                        if (null == t) throw new Error("First argument needs to be a string.");
                        var n = r(t.toString());
                        this.checksum = i(1, n)
                    }), t && (e.fromBuffer = function(t) {
                        return t.prototype = a, t
                    }(function(t) {
                        if (!(this instanceof e)) throw new TypeError("Constructor cannot called be as a function.");
                        if (!n(t)) throw new Error("First argument needs to be ArrayBuffer.");
                        var r = new Uint8Array(t);
                        return this.checksum = o(1, r)
                    })), a.update = function(t) {
                        if (null == t) throw new Error("First argument needs to be a string.");
                        return t = t.toString(), this.checksum = i(this.checksum, t)
                    }, a.updateUtf8 = function(t) {
                        if (null == t) throw new Error("First argument needs to be a string.");
                        var e = r(t.toString());
                        return this.checksum = i(this.checksum, e)
                    }, t && (a.updateBuffer = function(t) {
                        if (!n(t)) throw new Error("First argument needs to be ArrayBuffer.");
                        var e = new Uint8Array(t);
                        return this.checksum = o(this.checksum, e)
                    }), a.clone = function() {
                        return new s(this.checksum)
                    }, e
                }();
            return a.from = function(t) {
                if (null == t) throw new Error("First argument needs to be a string.");
                return i(1, t.toString())
            }, a.fromUtf8 = function(t) {
                if (null == t) throw new Error("First argument needs to be a string.");
                var e = r(t.toString());
                return i(1, e)
            }, t && (a.fromBuffer = function(t) {
                if (!n(t)) throw new Error("First argument need to be ArrayBuffer.");
                var e = new Uint8Array(t);
                return o(1, e)
            }), a
        });
    /**
     * CssColors
     * Copyright (c) 2014 Steven Spungin (TwelveTone LLC)  steven@twelvetone.tv
     *
     * Licensed under the MIT License.
     * http://opensource.org/licenses/mit-license
     */
    var i = {};
    i._colorsTable = {
        aliceblue: "#f0f8ff",
        antiquewhite: "#faebd7",
        aqua: "#00ffff",
        aquamarine: "#7fffd4",
        azure: "#f0ffff",
        beige: "#f5f5dc",
        bisque: "#ffe4c4",
        black: "#000000",
        blanchedalmond: "#ffebcd",
        blue: "#0000ff",
        blueviolet: "#8a2be2",
        brown: "#a52a2a",
        burlywood: "#deb887",
        cadetblue: "#5f9ea0",
        chartreuse: "#7fff00",
        chocolate: "#d2691e",
        coral: "#ff7f50",
        cornflowerblue: "#6495ed",
        cornsilk: "#fff8dc",
        crimson: "#dc143c",
        cyan: "#00ffff",
        darkblue: "#00008b",
        darkcyan: "#008b8b",
        darkgoldenrod: "#b8860b",
        darkgray: "#a9a9a9",
        darkgreen: "#006400",
        darkkhaki: "#bdb76b",
        darkmagenta: "#8b008b",
        darkolivegreen: "#556b2f",
        darkorange: "#ff8c00",
        darkorchid: "#9932cc",
        darkred: "#8b0000",
        darksalmon: "#e9967a",
        darkseagreen: "#8fbc8f",
        darkslateblue: "#483d8b",
        darkslategray: "#2f4f4f",
        darkturquoise: "#00ced1",
        darkviolet: "#9400d3",
        deeppink: "#ff1493",
        deepskyblue: "#00bfff",
        dimgray: "#696969",
        dodgerblue: "#1e90ff",
        firebrick: "#b22222",
        floralwhite: "#fffaf0",
        forestgreen: "#228b22",
        fuchsia: "#ff00ff",
        gainsboro: "#dcdcdc",
        ghostwhite: "#f8f8ff",
        gold: "#ffd700",
        goldenrod: "#daa520",
        gray: "#808080",
        green: "#008000",
        greenyellow: "#adff2f",
        honeydew: "#f0fff0",
        hotpink: "#ff69b4",
        "indianred ": "#cd5c5c",
        indigo: "#4b0082",
        ivory: "#fffff0",
        khaki: "#f0e68c",
        lavender: "#e6e6fa",
        lavenderblush: "#fff0f5",
        lawngreen: "#7cfc00",
        lemonchiffon: "#fffacd",
        lightblue: "#add8e6",
        lightcoral: "#f08080",
        lightcyan: "#e0ffff",
        lightgoldenrodyellow: "#fafad2",
        lightgrey: "#d3d3d3",
        lightgreen: "#90ee90",
        lightpink: "#ffb6c1",
        lightsalmon: "#ffa07a",
        lightseagreen: "#20b2aa",
        lightskyblue: "#87cefa",
        lightslategray: "#778899",
        lightsteelblue: "#b0c4de",
        lightyellow: "#ffffe0",
        lime: "#00ff00",
        limegreen: "#32cd32",
        linen: "#faf0e6",
        magenta: "#ff00ff",
        maroon: "#800000",
        mediumaquamarine: "#66cdaa",
        mediumblue: "#0000cd",
        mediumorchid: "#ba55d3",
        mediumpurple: "#9370d8",
        mediumseagreen: "#3cb371",
        mediumslateblue: "#7b68ee",
        mediumspringgreen: "#00fa9a",
        mediumturquoise: "#48d1cc",
        mediumvioletred: "#c71585",
        midnightblue: "#191970",
        mintcream: "#f5fffa",
        mistyrose: "#ffe4e1",
        moccasin: "#ffe4b5",
        navajowhite: "#ffdead",
        navy: "#000080",
        oldlace: "#fdf5e6",
        olive: "#808000",
        olivedrab: "#6b8e23",
        orange: "#ffa500",
        orangered: "#ff4500",
        orchid: "#da70d6",
        palegoldenrod: "#eee8aa",
        palegreen: "#98fb98",
        paleturquoise: "#afeeee",
        palevioletred: "#d87093",
        papayawhip: "#ffefd5",
        peachpuff: "#ffdab9",
        peru: "#cd853f",
        pink: "#ffc0cb",
        plum: "#dda0dd",
        powderblue: "#b0e0e6",
        purple: "#800080",
        red: "#ff0000",
        rosybrown: "#bc8f8f",
        royalblue: "#4169e1",
        saddlebrown: "#8b4513",
        salmon: "#fa8072",
        sandybrown: "#f4a460",
        seagreen: "#2e8b57",
        seashell: "#fff5ee",
        sienna: "#a0522d",
        silver: "#c0c0c0",
        skyblue: "#87ceeb",
        slateblue: "#6a5acd",
        slategray: "#708090",
        snow: "#fffafa",
        springgreen: "#00ff7f",
        steelblue: "#4682b4",
        tan: "#d2b48c",
        teal: "#008080",
        thistle: "#d8bfd8",
        tomato: "#ff6347",
        turquoise: "#40e0d0",
        violet: "#ee82ee",
        wheat: "#f5deb3",
        white: "#ffffff",
        whitesmoke: "#f5f5f5",
        yellow: "#ffff00",
        yellowgreen: "#9acd32"
    }, i.colorNameToHex = function(t) {
        return t = t.toLowerCase(), void 0 !== this._colorsTable[t] && this._colorsTable[t]
    };
    /*
     Deflate.js - https://github.com/gildas-lormeau/zip.js
     Copyright (c) 2013 Gildas Lormeau. All rights reserved.

     Redistribution and use in source and binary forms, with or without
     modification, are permitted provided that the following conditions are met:

     1. Redistributions of source code must retain the above copyright notice,
     this list of conditions and the following disclaimer.

     2. Redistributions in binary form must reproduce the above copyright 
     notice, this list of conditions and the following disclaimer in 
     the documentation and/or other materials provided with the distribution.

     3. The names of the authors may not be used to endorse or promote products
     derived from this software without specific prior written permission.

     THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESSED OR IMPLIED WARRANTIES,
     INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND
     FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL JCRAFT,
     INC. OR ANY CONTRIBUTORS TO THIS SOFTWARE BE LIABLE FOR ANY DIRECT, INDIRECT,
     INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
     LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA,
     OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
     LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
     NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE,
     EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
     */
    var o = function(t) {
        function e() {
            function t(t) {
                var e, n, i, o, a, c, l = r.dyn_tree,
                    u = r.stat_desc.static_tree,
                    h = r.stat_desc.extra_bits,
                    d = r.stat_desc.extra_base,
                    p = r.stat_desc.max_length,
                    m = 0;
                for (o = 0; o <= s; o++) t.bl_count[o] = 0;
                for (l[2 * t.heap[t.heap_max] + 1] = 0, e = t.heap_max + 1; e < f; e++)(o = l[2 * l[2 * (n = t.heap[e]) + 1] + 1] + 1) > p && (o = p, m++), l[2 * n + 1] = o, n > r.max_code || (t.bl_count[o]++, a = 0, n >= d && (a = h[n - d]), c = l[2 * n], t.opt_len += c * (o + a), u && (t.static_len += c * (u[2 * n + 1] + a)));
                if (0 !== m) {
                    do {
                        for (o = p - 1; 0 === t.bl_count[o];) o--;
                        t.bl_count[o]--, t.bl_count[o + 1] += 2, t.bl_count[p]--, m -= 2
                    } while (m > 0);
                    for (o = p; 0 !== o; o--)
                        for (n = t.bl_count[o]; 0 !== n;)(i = t.heap[--e]) > r.max_code || (l[2 * i + 1] != o && (t.opt_len += (o - l[2 * i + 1]) * l[2 * i], l[2 * i + 1] = o), n--)
                }
            }

            function e(t, e) {
                var n = 0;
                do {
                    n |= 1 & t, t >>>= 1, n <<= 1
                } while (--e > 0);
                return n >>> 1
            }

            function n(t, n, r) {
                var i, o, a, c = [],
                    l = 0;
                for (i = 1; i <= s; i++) c[i] = l = l + r[i - 1] << 1;
                for (o = 0; o <= n; o++) 0 !== (a = t[2 * o + 1]) && (t[2 * o] = e(c[a]++, a))
            }
            var r = this;
            r.build_tree = function(e) {
                var i, o, a, s = r.dyn_tree,
                    c = r.stat_desc.static_tree,
                    l = r.stat_desc.elems,
                    u = -1;
                for (e.heap_len = 0, e.heap_max = f, i = 0; i < l; i++) 0 !== s[2 * i] ? (e.heap[++e.heap_len] = u = i, e.depth[i] = 0) : s[2 * i + 1] = 0;
                for (; e.heap_len < 2;) s[2 * (a = e.heap[++e.heap_len] = u < 2 ? ++u : 0)] = 1, e.depth[a] = 0, e.opt_len--, c && (e.static_len -= c[2 * a + 1]);
                for (r.max_code = u, i = Math.floor(e.heap_len / 2); i >= 1; i--) e.pqdownheap(s, i);
                a = l;
                do {
                    i = e.heap[1], e.heap[1] = e.heap[e.heap_len--], e.pqdownheap(s, 1), o = e.heap[1], e.heap[--e.heap_max] = i, e.heap[--e.heap_max] = o, s[2 * a] = s[2 * i] + s[2 * o], e.depth[a] = Math.max(e.depth[i], e.depth[o]) + 1, s[2 * i + 1] = s[2 * o + 1] = a, e.heap[1] = a++, e.pqdownheap(s, 1)
                } while (e.heap_len >= 2);
                e.heap[--e.heap_max] = e.heap[1], t(e), n(s, r.max_code, e.bl_count)
            }
        }

        function n(t, e, n, r, i) {
            var o = this;
            o.static_tree = t, o.extra_bits = e, o.extra_base = n, o.elems = r, o.max_length = i
        }

        function r(t, e, n, r, i) {
            var o = this;
            o.good_length = t, o.max_lazy = e, o.nice_length = n, o.max_chain = r, o.func = i
        }

        function i(t, e, n, r) {
            var i = t[2 * e],
                o = t[2 * n];
            return i < o || i == o && r[e] <= r[n]
        }

        function o() {
            function t() {
                var t;
                for (Pt = 2 * Ct, Et[Bt - 1] = 0, t = 0; t < Bt - 1; t++) Et[t] = 0;
                Xt = N[Gt].max_lazy, Jt = N[Gt].good_length, Qt = N[Gt].nice_length, Vt = N[Gt].max_chain, Mt = 0, jt = 0, Ht = 0, Nt = Wt = $ - 1, Lt = 0, Ot = 0
            }

            function r() {
                var t;
                for (t = 0; t < h; t++) Kt[2 * t] = 0;
                for (t = 0; t < c; t++) $t[2 * t] = 0;
                for (t = 0; t < l; t++) Zt[2 * t] = 0;
                Kt[2 * d] = 1, te.opt_len = te.static_len = 0, ae = ce = 0
            }

            function o() {
                ee.dyn_tree = Kt, ee.stat_desc = n.static_l_desc, ne.dyn_tree = $t, ne.stat_desc = n.static_d_desc, re.dyn_tree = Zt, re.stat_desc = n.static_bl_desc, ue = 0, he = 0, le = 8, r()
            }

            function a(t, e) {
                var n, r, i = -1,
                    o = t[1],
                    a = 0,
                    s = 7,
                    c = 4;
                for (0 === o && (s = 138, c = 3), t[2 * (e + 1) + 1] = 65535, n = 0; n <= e; n++) r = o, o = t[2 * (n + 1) + 1], ++a < s && r == o || (a < c ? Zt[2 * r] += a : 0 !== r ? (r != i && Zt[2 * r]++, Zt[2 * p]++) : a <= 10 ? Zt[2 * m]++ : Zt[2 * g]++, a = 0, i = r, 0 === o ? (s = 138, c = 3) : r == o ? (s = 6, c = 3) : (s = 7, c = 4))
            }

            function s() {
                var t;
                for (a(Kt, ee.max_code), a($t, ne.max_code), re.build_tree(te), t = l - 1; t >= 3 && 0 === Zt[2 * e.bl_order[t] + 1]; t--);
                return te.opt_len += 3 * (t + 1) + 5 + 5 + 4, t
            }

            function f(t) {
                te.pending_buf[te.pending++] = t
            }

            function O(t) {
                f(255 & t), f(t >>> 8 & 255)
            }

            function et(t) {
                f(t >> 8 & 255), f(255 & t)
            }

            function nt(t, e) {
                var n, r = e;
                he > w - r ? (O(ue |= (n = t) << he & 65535), ue = n >>> w - he, he += r - w) : (ue |= t << he & 65535, he += r)
            }

            function rt(t, e) {
                var n = 2 * t;
                nt(65535 & e[n], 65535 & e[n + 1])
            }

            function it(t, e) {
                var n, r, i = -1,
                    o = t[1],
                    a = 0,
                    s = 7,
                    c = 4;
                for (0 === o && (s = 138, c = 3), n = 0; n <= e; n++)
                    if (r = o, o = t[2 * (n + 1) + 1], !(++a < s && r == o)) {
                        if (a < c)
                            do {
                                rt(r, Zt)
                            } while (0 != --a);
                        else 0 !== r ? (r != i && (rt(r, Zt), a--), rt(p, Zt), nt(a - 3, 2)) : a <= 10 ? (rt(m, Zt), nt(a - 3, 3)) : (rt(g, Zt), nt(a - 11, 7));
                        a = 0, i = r, 0 === o ? (s = 138, c = 3) : r == o ? (s = 6, c = 3) : (s = 7, c = 4)
                    }
            }

            function ot(t, n, r) {
                var i;
                for (nt(t - 257, 5), nt(n - 1, 5), nt(r - 4, 4), i = 0; i < r; i++) nt(Zt[2 * e.bl_order[i] + 1], 3);
                it(Kt, t - 1), it($t, n - 1)
            }

            function at() {
                16 == he ? (O(ue), ue = 0, he = 0) : he >= 8 && (f(255 & ue), ue >>>= 8, he -= 8)
            }

            function st() {
                nt(Q << 1, 3), rt(d, n.static_ltree), at(), 1 + le + 10 - he < 9 && (nt(Q << 1, 3), rt(d, n.static_ltree), at()), le = 7
            }

            function ct(t, n) {
                var r, i, o;
                if (te.pending_buf[se + 2 * ae] = t >>> 8 & 255, te.pending_buf[se + 2 * ae + 1] = 255 & t, te.pending_buf[ie + ae] = 255 & n, ae++, 0 === t ? Kt[2 * n]++ : (ce++, t--, Kt[2 * (e._length_code[n] + u + 1)]++, $t[2 * e.d_code(t)]++), 0 == (8191 & ae) && Gt > 2) {
                    for (r = 8 * ae, i = Mt - jt, o = 0; o < c; o++) r += $t[2 * o] * (5 + e.extra_dbits[o]);
                    if (r >>>= 3, ce < Math.floor(ae / 2) && r < Math.floor(i / 2)) return !0
                }
                return ae == oe - 1
            }

            function lt(t, n) {
                var r, i, o, a, s = 0;
                if (0 !== ae)
                    do {
                        r = te.pending_buf[se + 2 * s] << 8 & 65280 | 255 & te.pending_buf[se + 2 * s + 1], i = 255 & te.pending_buf[ie + s], s++, 0 === r ? rt(i, t) : (rt((o = e._length_code[i]) + u + 1, t), 0 !== (a = e.extra_lbits[o]) && nt(i -= e.base_length[o], a), r--, rt(o = e.d_code(r), n), 0 !== (a = e.extra_dbits[o]) && nt(r -= e.base_dist[o], a))
                    } while (s < ae);
                rt(d, t), le = t[2 * d + 1]
            }

            function ut() {
                he > 8 ? O(ue) : he > 0 && f(255 & ue), ue = 0, he = 0
            }

            function ht(t, e, n) {
                ut(), le = 8, n && (O(e), O(~e)), te.pending_buf.set(Tt.subarray(t, t + e), te.pending), te.pending += e
            }

            function ft(t, e, n) {
                nt((J << 1) + (n ? 1 : 0), 3), ht(t, e, !0)
            }

            function dt(t, e, i) {
                var o, a, c = 0;
                Gt > 0 ? (ee.build_tree(te), ne.build_tree(te), c = s(), o = te.opt_len + 3 + 7 >>> 3, (a = te.static_len + 3 + 7 >>> 3) <= o && (o = a)) : o = a = e + 5, e + 4 <= o && -1 != t ? ft(t, e, i) : a == o ? (nt((Q << 1) + (i ? 1 : 0), 3), lt(n.static_ltree, n.static_dtree)) : (nt((K << 1) + (i ? 1 : 0), 3), ot(ee.max_code + 1, ne.max_code + 1, c + 1), lt(Kt, $t)), r(), i && ut()
            }

            function pt(t) {
                dt(jt >= 0 ? jt : -1, Mt - jt, t), jt = Mt, xt.flush_pending()
            }

            function mt() {
                var t, e, n, r;
                do {
                    if (0 === (r = Pt - Ht - Mt) && 0 === Mt && 0 === Ht) r = Ct;
                    else if (-1 == r) r--;
                    else if (Mt >= Ct + Ct - tt) {
                        Tt.set(Tt.subarray(Ct, Ct + Ct), 0), Ut -= Ct, Mt -= Ct, jt -= Ct, n = t = Bt;
                        do {
                            e = 65535 & Et[--n], Et[n] = e >= Ct ? e - Ct : 0
                        } while (0 != --t);
                        n = t = Ct;
                        do {
                            e = 65535 & It[--n], It[n] = e >= Ct ? e - Ct : 0
                        } while (0 != --t);
                        r += Ct
                    }
                    if (0 === xt.avail_in) return;
                    t = xt.read_buf(Tt, Mt + Ht, r), (Ht += t) >= $ && (Ot = 255 & Tt[Mt], Ot = (Ot << Dt ^ 255 & Tt[Mt + 1]) & Rt)
                } while (Ht < tt && 0 !== xt.avail_in)
            }

            function gt(t) {
                var e, n = 65535;
                for (n > _t - 5 && (n = _t - 5);;) {
                    if (Ht <= 1) {
                        if (mt(), 0 === Ht && t == k) return L;
                        if (0 === Ht) break
                    }
                    if (Mt += Ht, Ht = 0, e = jt + n, (0 === Mt || Mt >= e) && (Ht = Mt - e, Mt = e, pt(!1), 0 === xt.avail_out)) return L;
                    if (Mt - jt >= Ct - tt && (pt(!1), 0 === xt.avail_out)) return L
                }
                return pt(t == C), 0 === xt.avail_out ? t == C ? U : L : t == C ? H : M
            }

            function wt(t) {
                var e, n, r = Vt,
                    i = Mt,
                    o = Wt,
                    a = Mt > Ct - tt ? Mt - (Ct - tt) : 0,
                    s = Qt,
                    c = qt,
                    l = Mt + Z,
                    u = Tt[i + o - 1],
                    h = Tt[i + o];
                Wt >= Jt && (r >>= 2), s > Ht && (s = Ht);
                do {
                    if (e = t, Tt[e + o] == h && Tt[e + o - 1] == u && Tt[e] == Tt[i] && Tt[++e] == Tt[i + 1]) {
                        i += 2, e++;
                        do {} while (Tt[++i] == Tt[++e] && Tt[++i] == Tt[++e] && Tt[++i] == Tt[++e] && Tt[++i] == Tt[++e] && Tt[++i] == Tt[++e] && Tt[++i] == Tt[++e] && Tt[++i] == Tt[++e] && Tt[++i] == Tt[++e] && i < l);
                        if (n = Z - (l - i), i = l - Z, n > o) {
                            if (Ut = t, o = n, n >= s) break;
                            u = Tt[i + o - 1], h = Tt[i + o]
                        }
                    }
                } while ((t = 65535 & It[t & c]) > a && 0 != --r);
                return o <= Ht ? o : Ht
            }

            function yt(t) {
                for (var e, n = 0;;) {
                    if (Ht < tt) {
                        if (mt(), Ht < tt && t == k) return L;
                        if (0 === Ht) break
                    }
                    if (Ht >= $ && (Ot = (Ot << Dt ^ 255 & Tt[Mt + ($ - 1)]) & Rt, n = 65535 & Et[Ot], It[Mt & qt] = Et[Ot], Et[Ot] = Mt), 0 !== n && (Mt - n & 65535) <= Ct - tt && Yt != b && (Nt = wt(n)), Nt >= $)
                        if (e = ct(Mt - Ut, Nt - $), Ht -= Nt, Nt <= Xt && Ht >= $) {
                            Nt--;
                            do {
                                Ot = (Ot << Dt ^ 255 & Tt[++Mt + ($ - 1)]) & Rt, n = 65535 & Et[Ot], It[Mt & qt] = Et[Ot], Et[Ot] = Mt
                            } while (0 != --Nt);
                            Mt++
                        } else Mt += Nt, Nt = 0, Ot = 255 & Tt[Mt], Ot = (Ot << Dt ^ 255 & Tt[Mt + 1]) & Rt;
                    else e = ct(0, 255 & Tt[Mt]), Ht--, Mt++;
                    if (e && (pt(!1), 0 === xt.avail_out)) return L
                }
                return pt(t == C), 0 === xt.avail_out ? t == C ? U : L : t == C ? H : M
            }

            function vt(t) {
                for (var e, n, r = 0;;) {
                    if (Ht < tt) {
                        if (mt(), Ht < tt && t == k) return L;
                        if (0 === Ht) break
                    }
                    if (Ht >= $ && (Ot = (Ot << Dt ^ 255 & Tt[Mt + ($ - 1)]) & Rt, r = 65535 & Et[Ot], It[Mt & qt] = Et[Ot], Et[Ot] = Mt), Wt = Nt, zt = Ut, Nt = $ - 1, 0 !== r && Wt < Xt && (Mt - r & 65535) <= Ct - tt && (Yt != b && (Nt = wt(r)), Nt <= 5 && (Yt == v || Nt == $ && Mt - Ut > 4096) && (Nt = $ - 1)), Wt >= $ && Nt <= Wt) {
                        n = Mt + Ht - $, e = ct(Mt - 1 - zt, Wt - $), Ht -= Wt - 1, Wt -= 2;
                        do {
                            ++Mt <= n && (Ot = (Ot << Dt ^ 255 & Tt[Mt + ($ - 1)]) & Rt, r = 65535 & Et[Ot], It[Mt & qt] = Et[Ot], Et[Ot] = Mt)
                        } while (0 != --Wt);
                        if (Lt = 0, Nt = $ - 1, Mt++, e && (pt(!1), 0 === xt.avail_out)) return L
                    } else if (0 !== Lt) {
                        if ((e = ct(0, 255 & Tt[Mt - 1])) && pt(!1), Mt++, Ht--, 0 === xt.avail_out) return L
                    } else Lt = 1, Mt++, Ht--
                }
                return 0 !== Lt && (e = ct(0, 255 & Tt[Mt - 1]), Lt = 0), pt(t == C), 0 === xt.avail_out ? t == C ? U : L : t == C ? H : M
            }

            function bt(e) {
                return e.total_in = e.total_out = 0, e.msg = null, te.pending = 0, te.pending_out = 0, kt = X, St = k, o(), t(), A
            }
            var xt, kt, _t, St, Ct, At, qt, Tt, Pt, It, Et, Ot, Bt, Ft, Rt, Dt, jt, Nt, zt, Lt, Mt, Ut, Ht, Wt, Vt, Xt, Gt, Yt, Jt, Qt, Kt, $t, Zt, te = this,
                ee = new e,
                ne = new e,
                re = new e;
            te.depth = [];
            var ie, oe, ae, se, ce, le, ue, he;
            te.bl_count = [], te.heap = [], Kt = [], $t = [], Zt = [], te.pqdownheap = function(t, e) {
                for (var n = te.heap, r = n[e], o = e << 1; o <= te.heap_len && (o < te.heap_len && i(t, n[o + 1], n[o], te.depth) && o++, !i(t, r, n[o], te.depth));) n[e] = n[o], e = o, o <<= 1;
                n[e] = r
            }, te.deflateInit = function(t, e, n, r, i, o) {
                return r || (r = Y), i || (i = F), o || (o = x), t.msg = null, e == y && (e = 6), i < 1 || i > B || r != Y || n < 9 || n > 15 || e < 0 || e > 9 || o < 0 || o > b ? P : (t.dstate = te, At = n, Ct = 1 << At, qt = Ct - 1, Ft = i + 7, Bt = 1 << Ft, Rt = Bt - 1, Dt = Math.floor((Ft + $ - 1) / $), Tt = new Uint8Array(2 * Ct), It = [], Et = [], oe = 1 << i + 6, te.pending_buf = new Uint8Array(4 * oe), _t = 4 * oe, se = Math.floor(oe / 2), ie = 3 * oe, Gt = e, Yt = o, bt(t))
            }, te.deflateEnd = function() {
                return kt != V && kt != X && kt != G ? P : (te.pending_buf = null, Et = null, It = null, Tt = null, te.dstate = null, kt == X ? I : A)
            }, te.deflateParams = function(t, e, n) {
                var r = A;
                return e == y && (e = 6), e < 0 || e > 9 || n < 0 || n > b ? P : (N[Gt].func != N[e].func && 0 !== t.total_in && (r = t.deflate(_)), Gt != e && (Xt = N[Gt = e].max_lazy, Jt = N[Gt].good_length, Qt = N[Gt].nice_length, Vt = N[Gt].max_chain), Yt = n, r)
            }, te.deflateSetDictionary = function(t, e, n) {
                var r, i = n,
                    o = 0;
                if (!e || kt != V) return P;
                if (i < $) return A;
                for (i > Ct - tt && (o = n - (i = Ct - tt)), Tt.set(e.subarray(o, o + i), 0), Mt = i, jt = i, Ot = 255 & Tt[0], Ot = (Ot << Dt ^ 255 & Tt[1]) & Rt, r = 0; r <= i - $; r++) Ot = (Ot << Dt ^ 255 & Tt[r + ($ - 1)]) & Rt, It[r & qt] = Et[Ot], Et[Ot] = r;
                return A
            }, te.deflate = function(t, e) {
                var n, r, i, o, a;
                if (e > C || e < 0) return P;
                if (!t.next_out || !t.next_in && 0 !== t.avail_in || kt == G && e != C) return t.msg = z[T - P], P;
                if (0 === t.avail_out) return t.msg = z[T - E], E;
                if (xt = t, o = St, St = e, kt == V && (r = Y + (At - 8 << 4) << 8, (i = (Gt - 1 & 255) >> 1) > 3 && (i = 3), r |= i << 6, 0 !== Mt && (r |= W), kt = X, et(r += 31 - r % 31)), 0 !== te.pending) {
                    if (xt.flush_pending(), 0 === xt.avail_out) return St = -1, A
                } else if (0 === xt.avail_in && e <= o && e != C) return xt.msg = z[T - E], E;
                if (kt == G && 0 !== xt.avail_in) return t.msg = z[T - E], E;
                if (0 !== xt.avail_in || 0 !== Ht || e != k && kt != G) {
                    switch (a = -1, N[Gt].func) {
                        case R:
                            a = gt(e);
                            break;
                        case D:
                            a = yt(e);
                            break;
                        case j:
                            a = vt(e)
                    }
                    if (a != U && a != H || (kt = G), a == L || a == U) return 0 === xt.avail_out && (St = -1), A;
                    if (a == M) {
                        if (e == _) st();
                        else if (ft(0, 0, !1), e == S)
                            for (n = 0; n < Bt; n++) Et[n] = 0;
                        if (xt.flush_pending(), 0 === xt.avail_out) return St = -1, A
                    }
                }
                return e != C ? A : q
            }
        }

        function a() {
            var t = this;
            t.next_in_index = 0, t.next_out_index = 0, t.avail_in = 0, t.total_in = 0, t.avail_out = 0, t.total_out = 0
        }
        var s = 15,
            c = 30,
            l = 19,
            u = 256,
            h = u + 1 + 29,
            f = 2 * h + 1,
            d = 256,
            p = 16,
            m = 17,
            g = 18,
            w = 16,
            y = -1,
            v = 1,
            b = 2,
            x = 0,
            k = 0,
            _ = 1,
            S = 3,
            C = 4,
            A = 0,
            q = 1,
            T = 2,
            P = -2,
            I = -3,
            E = -5,
            O = [0, 1, 2, 3, 4, 4, 5, 5, 6, 6, 6, 6, 7, 7, 7, 7, 8, 8, 8, 8, 8, 8, 8, 8, 9, 9, 9, 9, 9, 9, 9, 9, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 11, 11, 11, 11, 11, 11, 11, 11, 11, 11, 11, 11, 11, 11, 11, 11, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 13, 13, 13, 13, 13, 13, 13, 13, 13, 13, 13, 13, 13, 13, 13, 13, 13, 13, 13, 13, 13, 13, 13, 13, 13, 13, 13, 13, 13, 13, 13, 13, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 0, 0, 16, 17, 18, 18, 19, 19, 20, 20, 20, 20, 21, 21, 21, 21, 22, 22, 22, 22, 22, 22, 22, 22, 23, 23, 23, 23, 23, 23, 23, 23, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 25, 25, 25, 25, 25, 25, 25, 25, 25, 25, 25, 25, 25, 25, 25, 25, 26, 26, 26, 26, 26, 26, 26, 26, 26, 26, 26, 26, 26, 26, 26, 26, 26, 26, 26, 26, 26, 26, 26, 26, 26, 26, 26, 26, 26, 26, 26, 26, 27, 27, 27, 27, 27, 27, 27, 27, 27, 27, 27, 27, 27, 27, 27, 27, 27, 27, 27, 27, 27, 27, 27, 27, 27, 27, 27, 27, 27, 27, 27, 27, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29];
        e._length_code = [0, 1, 2, 3, 4, 5, 6, 7, 8, 8, 9, 9, 10, 10, 11, 11, 12, 12, 12, 12, 13, 13, 13, 13, 14, 14, 14, 14, 15, 15, 15, 15, 16, 16, 16, 16, 16, 16, 16, 16, 17, 17, 17, 17, 17, 17, 17, 17, 18, 18, 18, 18, 18, 18, 18, 18, 19, 19, 19, 19, 19, 19, 19, 19, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 21, 21, 21, 21, 21, 21, 21, 21, 21, 21, 21, 21, 21, 21, 21, 21, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 22, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 23, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 25, 25, 25, 25, 25, 25, 25, 25, 25, 25, 25, 25, 25, 25, 25, 25, 25, 25, 25, 25, 25, 25, 25, 25, 25, 25, 25, 25, 25, 25, 25, 25, 26, 26, 26, 26, 26, 26, 26, 26, 26, 26, 26, 26, 26, 26, 26, 26, 26, 26, 26, 26, 26, 26, 26, 26, 26, 26, 26, 26, 26, 26, 26, 26, 27, 27, 27, 27, 27, 27, 27, 27, 27, 27, 27, 27, 27, 27, 27, 27, 27, 27, 27, 27, 27, 27, 27, 27, 27, 27, 27, 27, 27, 27, 27, 28], e.base_length = [0, 1, 2, 3, 4, 5, 6, 7, 8, 10, 12, 14, 16, 20, 24, 28, 32, 40, 48, 56, 64, 80, 96, 112, 128, 160, 192, 224, 0], e.base_dist = [0, 1, 2, 3, 4, 6, 8, 12, 16, 24, 32, 48, 64, 96, 128, 192, 256, 384, 512, 768, 1024, 1536, 2048, 3072, 4096, 6144, 8192, 12288, 16384, 24576], e.d_code = function(t) {
            return t < 256 ? O[t] : O[256 + (t >>> 7)]
        }, e.extra_lbits = [0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 4, 4, 4, 5, 5, 5, 5, 0], e.extra_dbits = [0, 0, 0, 0, 1, 1, 2, 2, 3, 3, 4, 4, 5, 5, 6, 6, 7, 7, 8, 8, 9, 9, 10, 10, 11, 11, 12, 12, 13, 13], e.extra_blbits = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 3, 7], e.bl_order = [16, 17, 18, 0, 8, 7, 9, 6, 10, 5, 11, 4, 12, 3, 13, 2, 14, 1, 15], n.static_ltree = [12, 8, 140, 8, 76, 8, 204, 8, 44, 8, 172, 8, 108, 8, 236, 8, 28, 8, 156, 8, 92, 8, 220, 8, 60, 8, 188, 8, 124, 8, 252, 8, 2, 8, 130, 8, 66, 8, 194, 8, 34, 8, 162, 8, 98, 8, 226, 8, 18, 8, 146, 8, 82, 8, 210, 8, 50, 8, 178, 8, 114, 8, 242, 8, 10, 8, 138, 8, 74, 8, 202, 8, 42, 8, 170, 8, 106, 8, 234, 8, 26, 8, 154, 8, 90, 8, 218, 8, 58, 8, 186, 8, 122, 8, 250, 8, 6, 8, 134, 8, 70, 8, 198, 8, 38, 8, 166, 8, 102, 8, 230, 8, 22, 8, 150, 8, 86, 8, 214, 8, 54, 8, 182, 8, 118, 8, 246, 8, 14, 8, 142, 8, 78, 8, 206, 8, 46, 8, 174, 8, 110, 8, 238, 8, 30, 8, 158, 8, 94, 8, 222, 8, 62, 8, 190, 8, 126, 8, 254, 8, 1, 8, 129, 8, 65, 8, 193, 8, 33, 8, 161, 8, 97, 8, 225, 8, 17, 8, 145, 8, 81, 8, 209, 8, 49, 8, 177, 8, 113, 8, 241, 8, 9, 8, 137, 8, 73, 8, 201, 8, 41, 8, 169, 8, 105, 8, 233, 8, 25, 8, 153, 8, 89, 8, 217, 8, 57, 8, 185, 8, 121, 8, 249, 8, 5, 8, 133, 8, 69, 8, 197, 8, 37, 8, 165, 8, 101, 8, 229, 8, 21, 8, 149, 8, 85, 8, 213, 8, 53, 8, 181, 8, 117, 8, 245, 8, 13, 8, 141, 8, 77, 8, 205, 8, 45, 8, 173, 8, 109, 8, 237, 8, 29, 8, 157, 8, 93, 8, 221, 8, 61, 8, 189, 8, 125, 8, 253, 8, 19, 9, 275, 9, 147, 9, 403, 9, 83, 9, 339, 9, 211, 9, 467, 9, 51, 9, 307, 9, 179, 9, 435, 9, 115, 9, 371, 9, 243, 9, 499, 9, 11, 9, 267, 9, 139, 9, 395, 9, 75, 9, 331, 9, 203, 9, 459, 9, 43, 9, 299, 9, 171, 9, 427, 9, 107, 9, 363, 9, 235, 9, 491, 9, 27, 9, 283, 9, 155, 9, 411, 9, 91, 9, 347, 9, 219, 9, 475, 9, 59, 9, 315, 9, 187, 9, 443, 9, 123, 9, 379, 9, 251, 9, 507, 9, 7, 9, 263, 9, 135, 9, 391, 9, 71, 9, 327, 9, 199, 9, 455, 9, 39, 9, 295, 9, 167, 9, 423, 9, 103, 9, 359, 9, 231, 9, 487, 9, 23, 9, 279, 9, 151, 9, 407, 9, 87, 9, 343, 9, 215, 9, 471, 9, 55, 9, 311, 9, 183, 9, 439, 9, 119, 9, 375, 9, 247, 9, 503, 9, 15, 9, 271, 9, 143, 9, 399, 9, 79, 9, 335, 9, 207, 9, 463, 9, 47, 9, 303, 9, 175, 9, 431, 9, 111, 9, 367, 9, 239, 9, 495, 9, 31, 9, 287, 9, 159, 9, 415, 9, 95, 9, 351, 9, 223, 9, 479, 9, 63, 9, 319, 9, 191, 9, 447, 9, 127, 9, 383, 9, 255, 9, 511, 9, 0, 7, 64, 7, 32, 7, 96, 7, 16, 7, 80, 7, 48, 7, 112, 7, 8, 7, 72, 7, 40, 7, 104, 7, 24, 7, 88, 7, 56, 7, 120, 7, 4, 7, 68, 7, 36, 7, 100, 7, 20, 7, 84, 7, 52, 7, 116, 7, 3, 8, 131, 8, 67, 8, 195, 8, 35, 8, 163, 8, 99, 8, 227, 8], n.static_dtree = [0, 5, 16, 5, 8, 5, 24, 5, 4, 5, 20, 5, 12, 5, 28, 5, 2, 5, 18, 5, 10, 5, 26, 5, 6, 5, 22, 5, 14, 5, 30, 5, 1, 5, 17, 5, 9, 5, 25, 5, 5, 5, 21, 5, 13, 5, 29, 5, 3, 5, 19, 5, 11, 5, 27, 5, 7, 5, 23, 5], n.static_l_desc = new n(n.static_ltree, e.extra_lbits, u + 1, h, s), n.static_d_desc = new n(n.static_dtree, e.extra_dbits, 0, c, s), n.static_bl_desc = new n(null, e.extra_blbits, 0, l, 7);
        var B = 9,
            F = 8,
            R = 0,
            D = 1,
            j = 2,
            N = [new r(0, 0, 0, 0, R), new r(4, 4, 8, 4, D), new r(4, 5, 16, 8, D), new r(4, 6, 32, 32, D), new r(4, 4, 16, 16, j), new r(8, 16, 32, 32, j), new r(8, 16, 128, 128, j), new r(8, 32, 128, 256, j), new r(32, 128, 258, 1024, j), new r(32, 258, 258, 4096, j)],
            z = ["need dictionary", "stream end", "", "", "stream error", "data error", "", "buffer error", "", ""],
            L = 0,
            M = 1,
            U = 2,
            H = 3,
            W = 32,
            V = 42,
            X = 113,
            G = 666,
            Y = 8,
            J = 0,
            Q = 1,
            K = 2,
            $ = 3,
            Z = 258,
            tt = Z + $ + 1;
        return a.prototype = {
                deflateInit: function(t, e) {
                    var n = this;
                    return n.dstate = new o, e || (e = s), n.dstate.deflateInit(n, t, e)
                },
                deflate: function(t) {
                    var e = this;
                    return e.dstate ? e.dstate.deflate(e, t) : P
                },
                deflateEnd: function() {
                    var t = this;
                    if (!t.dstate) return P;
                    var e = t.dstate.deflateEnd();
                    return t.dstate = null, e
                },
                deflateParams: function(t, e) {
                    var n = this;
                    return n.dstate ? n.dstate.deflateParams(n, t, e) : P
                },
                deflateSetDictionary: function(t, e) {
                    var n = this;
                    return n.dstate ? n.dstate.deflateSetDictionary(n, t, e) : P
                },
                read_buf: function(t, e, n) {
                    var r = this,
                        i = r.avail_in;
                    return i > n && (i = n), 0 === i ? 0 : (r.avail_in -= i, t.set(r.next_in.subarray(r.next_in_index, r.next_in_index + i), e), r.next_in_index += i, r.total_in += i, i)
                },
                flush_pending: function() {
                    var t = this,
                        e = t.dstate.pending;
                    e > t.avail_out && (e = t.avail_out), 0 !== e && (t.next_out.set(t.dstate.pending_buf.subarray(t.dstate.pending_out, t.dstate.pending_out + e), t.next_out_index), t.next_out_index += e, t.dstate.pending_out += e, t.total_out += e, t.avail_out -= e, t.dstate.pending -= e, 0 === t.dstate.pending && (t.dstate.pending_out = 0))
                }
            },
            function(t) {
                var e = this,
                    n = new a,
                    r = k,
                    i = new Uint8Array(512);
                void 0 === t && (t = y), n.deflateInit(t), n.next_out = i, e.append = function(t, e) {
                    var o, a = [],
                        s = 0,
                        c = 0,
                        l = 0;
                    if (t.length) {
                        n.next_in_index = 0, n.next_in = t, n.avail_in = t.length;
                        do {
                            if (n.next_out_index = 0, n.avail_out = 512, n.deflate(r) != A) throw "deflating: " + n.msg;
                            n.next_out_index && (512 == n.next_out_index ? a.push(new Uint8Array(i)) : a.push(new Uint8Array(i.subarray(0, n.next_out_index)))), l += n.next_out_index, e && n.next_in_index > 0 && n.next_in_index != s && (e(n.next_in_index), s = n.next_in_index)
                        } while (n.avail_in > 0 || 0 === n.avail_out);
                        return o = new Uint8Array(l), a.forEach(function(t) {
                            o.set(t, c), c += t.length
                        }), o
                    }
                }, e.flush = function() {
                    var t, e, r = [],
                        o = 0,
                        a = 0;
                    do {
                        if (n.next_out_index = 0, n.avail_out = 512, (t = n.deflate(C)) != q && t != A) throw "deflating: " + n.msg;
                        512 - n.avail_out > 0 && r.push(new Uint8Array(i.subarray(0, n.next_out_index))), a += n.next_out_index
                    } while (n.avail_in > 0 || 0 === n.avail_out);
                    return n.deflateEnd(), e = new Uint8Array(a), r.forEach(function(t) {
                        e.set(t, o), o += t.length
                    }), e
                }
            }
    }();

    /*
     * Extracted from pdf.js
     * https://github.com/andreasgal/pdf.js
     *
     * Copyright (c) 2011 Mozilla Foundation
     *
     * Contributors: Andreas Gal <gal@mozilla.com>
     *               Chris G Jones <cjones@mozilla.com>
     *               Shaon Barman <shaon.barman@gmail.com>
     *               Vivien Nicolas <21@vingtetun.org>
     *               Justin D'Arcangelo <justindarc@gmail.com>
     *               Yury Delendik
     *
     * 
     */
    var a = function() {
            function t() {
                this.pos = 0, this.bufferLength = 0, this.eof = !1, this.buffer = null
            }
            return t.prototype = {
                ensureBuffer: function(t) {
                    var e = this.buffer,
                        n = e ? e.byteLength : 0;
                    if (t < n) return e;
                    for (var r = 512; r < t;) r <<= 1;
                    for (var i = new Uint8Array(r), o = 0; o < n; ++o) i[o] = e[o];
                    return this.buffer = i
                },
                getByte: function() {
                    for (var t = this.pos; this.bufferLength <= t;) {
                        if (this.eof) return null;
                        this.readBlock()
                    }
                    return this.buffer[this.pos++]
                },
                getBytes: function(t) {
                    var e = this.pos;
                    if (t) {
                        this.ensureBuffer(e + t);
                        for (r = e + t; !this.eof && this.bufferLength < r;) this.readBlock();
                        var n = this.bufferLength;
                        r > n && (r = n)
                    } else {
                        for (; !this.eof;) this.readBlock();
                        var r = this.bufferLength
                    }
                    return this.pos = r, this.buffer.subarray(e, r)
                },
                lookChar: function() {
                    for (var t = this.pos; this.bufferLength <= t;) {
                        if (this.eof) return null;
                        this.readBlock()
                    }
                    return String.fromCharCode(this.buffer[this.pos])
                },
                getChar: function() {
                    for (var t = this.pos; this.bufferLength <= t;) {
                        if (this.eof) return null;
                        this.readBlock()
                    }
                    return String.fromCharCode(this.buffer[this.pos++])
                },
                makeSubStream: function(t, e, n) {
                    for (var r = t + e; this.bufferLength <= r && !this.eof;) this.readBlock();
                    return new Stream(this.buffer, t, e, n)
                },
                skip: function(t) {
                    t || (t = 1), this.pos += t
                },
                reset: function() {
                    this.pos = 0
                }
            }, t
        }(),
        s = function() {
            function t(t) {
                throw new Error(t)
            }

            function e(e) {
                var n = 0,
                    r = e[n++],
                    i = e[n++]; - 1 != r && -1 != i || t("Invalid header in flate stream"), 8 != (15 & r) && t("Unknown compression method in flate stream"), ((r << 8) + i) % 31 != 0 && t("Bad FCHECK in flate stream"), 32 & i && t("FDICT bit set in flate stream"), this.bytes = e, this.bytesPos = n, this.codeSize = 0, this.codeBuf = 0, a.call(this)
            }
            if ("undefined" != typeof Uint32Array) {
                var n = new Uint32Array([16, 17, 18, 0, 8, 7, 9, 6, 10, 5, 11, 4, 12, 3, 13, 2, 14, 1, 15]),
                    r = new Uint32Array([3, 4, 5, 6, 7, 8, 9, 10, 65547, 65549, 65551, 65553, 131091, 131095, 131099, 131103, 196643, 196651, 196659, 196667, 262211, 262227, 262243, 262259, 327811, 327843, 327875, 327907, 258, 258, 258]),
                    i = new Uint32Array([1, 2, 3, 4, 65541, 65543, 131081, 131085, 196625, 196633, 262177, 262193, 327745, 327777, 393345, 393409, 459009, 459137, 524801, 525057, 590849, 591361, 657409, 658433, 724993, 727041, 794625, 798721, 868353, 876545]),
                    o = [new Uint32Array([459008, 524368, 524304, 524568, 459024, 524400, 524336, 590016, 459016, 524384, 524320, 589984, 524288, 524416, 524352, 590048, 459012, 524376, 524312, 589968, 459028, 524408, 524344, 590032, 459020, 524392, 524328, 59e4, 524296, 524424, 524360, 590064, 459010, 524372, 524308, 524572, 459026, 524404, 524340, 590024, 459018, 524388, 524324, 589992, 524292, 524420, 524356, 590056, 459014, 524380, 524316, 589976, 459030, 524412, 524348, 590040, 459022, 524396, 524332, 590008, 524300, 524428, 524364, 590072, 459009, 524370, 524306, 524570, 459025, 524402, 524338, 590020, 459017, 524386, 524322, 589988, 524290, 524418, 524354, 590052, 459013, 524378, 524314, 589972, 459029, 524410, 524346, 590036, 459021, 524394, 524330, 590004, 524298, 524426, 524362, 590068, 459011, 524374, 524310, 524574, 459027, 524406, 524342, 590028, 459019, 524390, 524326, 589996, 524294, 524422, 524358, 590060, 459015, 524382, 524318, 589980, 459031, 524414, 524350, 590044, 459023, 524398, 524334, 590012, 524302, 524430, 524366, 590076, 459008, 524369, 524305, 524569, 459024, 524401, 524337, 590018, 459016, 524385, 524321, 589986, 524289, 524417, 524353, 590050, 459012, 524377, 524313, 589970, 459028, 524409, 524345, 590034, 459020, 524393, 524329, 590002, 524297, 524425, 524361, 590066, 459010, 524373, 524309, 524573, 459026, 524405, 524341, 590026, 459018, 524389, 524325, 589994, 524293, 524421, 524357, 590058, 459014, 524381, 524317, 589978, 459030, 524413, 524349, 590042, 459022, 524397, 524333, 590010, 524301, 524429, 524365, 590074, 459009, 524371, 524307, 524571, 459025, 524403, 524339, 590022, 459017, 524387, 524323, 589990, 524291, 524419, 524355, 590054, 459013, 524379, 524315, 589974, 459029, 524411, 524347, 590038, 459021, 524395, 524331, 590006, 524299, 524427, 524363, 590070, 459011, 524375, 524311, 524575, 459027, 524407, 524343, 590030, 459019, 524391, 524327, 589998, 524295, 524423, 524359, 590062, 459015, 524383, 524319, 589982, 459031, 524415, 524351, 590046, 459023, 524399, 524335, 590014, 524303, 524431, 524367, 590078, 459008, 524368, 524304, 524568, 459024, 524400, 524336, 590017, 459016, 524384, 524320, 589985, 524288, 524416, 524352, 590049, 459012, 524376, 524312, 589969, 459028, 524408, 524344, 590033, 459020, 524392, 524328, 590001, 524296, 524424, 524360, 590065, 459010, 524372, 524308, 524572, 459026, 524404, 524340, 590025, 459018, 524388, 524324, 589993, 524292, 524420, 524356, 590057, 459014, 524380, 524316, 589977, 459030, 524412, 524348, 590041, 459022, 524396, 524332, 590009, 524300, 524428, 524364, 590073, 459009, 524370, 524306, 524570, 459025, 524402, 524338, 590021, 459017, 524386, 524322, 589989, 524290, 524418, 524354, 590053, 459013, 524378, 524314, 589973, 459029, 524410, 524346, 590037, 459021, 524394, 524330, 590005, 524298, 524426, 524362, 590069, 459011, 524374, 524310, 524574, 459027, 524406, 524342, 590029, 459019, 524390, 524326, 589997, 524294, 524422, 524358, 590061, 459015, 524382, 524318, 589981, 459031, 524414, 524350, 590045, 459023, 524398, 524334, 590013, 524302, 524430, 524366, 590077, 459008, 524369, 524305, 524569, 459024, 524401, 524337, 590019, 459016, 524385, 524321, 589987, 524289, 524417, 524353, 590051, 459012, 524377, 524313, 589971, 459028, 524409, 524345, 590035, 459020, 524393, 524329, 590003, 524297, 524425, 524361, 590067, 459010, 524373, 524309, 524573, 459026, 524405, 524341, 590027, 459018, 524389, 524325, 589995, 524293, 524421, 524357, 590059, 459014, 524381, 524317, 589979, 459030, 524413, 524349, 590043, 459022, 524397, 524333, 590011, 524301, 524429, 524365, 590075, 459009, 524371, 524307, 524571, 459025, 524403, 524339, 590023, 459017, 524387, 524323, 589991, 524291, 524419, 524355, 590055, 459013, 524379, 524315, 589975, 459029, 524411, 524347, 590039, 459021, 524395, 524331, 590007, 524299, 524427, 524363, 590071, 459011, 524375, 524311, 524575, 459027, 524407, 524343, 590031, 459019, 524391, 524327, 589999, 524295, 524423, 524359, 590063, 459015, 524383, 524319, 589983, 459031, 524415, 524351, 590047, 459023, 524399, 524335, 590015, 524303, 524431, 524367, 590079]), 9],
                    s = [new Uint32Array([327680, 327696, 327688, 327704, 327684, 327700, 327692, 327708, 327682, 327698, 327690, 327706, 327686, 327702, 327694, 0, 327681, 327697, 327689, 327705, 327685, 327701, 327693, 327709, 327683, 327699, 327691, 327707, 327687, 327703, 327695, 0]), 5];
                return e.prototype = Object.create(a.prototype), e.prototype.getBits = function(e) {
                    for (var n, r = this.codeSize, i = this.codeBuf, o = this.bytes, a = this.bytesPos; r < e;) void 0 === (n = o[a++]) && t("Bad encoding in flate stream"), i |= n << r, r += 8;
                    return n = i & (1 << e) - 1, this.codeBuf = i >> e, this.codeSize = r -= e, this.bytesPos = a, n
                }, e.prototype.getCode = function(e) {
                    for (var n = e[0], r = e[1], i = this.codeSize, o = this.codeBuf, a = this.bytes, s = this.bytesPos; i < r;) {
                        var c;
                        void 0 === (c = a[s++]) && t("Bad encoding in flate stream"), o |= c << i, i += 8
                    }
                    var l = n[o & (1 << r) - 1],
                        u = l >> 16,
                        h = 65535 & l;
                    return (0 == i || i < u || 0 == u) && t("Bad encoding in flate stream"), this.codeBuf = o >> u, this.codeSize = i - u, this.bytesPos = s, h
                }, e.prototype.generateHuffmanTable = function(t) {
                    for (var e = t.length, n = 0, r = 0; r < e; ++r) t[r] > n && (n = t[r]);
                    for (var i = 1 << n, o = new Uint32Array(i), a = 1, s = 0, c = 2; a <= n; ++a, s <<= 1, c <<= 1)
                        for (var l = 0; l < e; ++l)
                            if (t[l] == a) {
                                for (var u = 0, h = s, r = 0; r < a; ++r) u = u << 1 | 1 & h, h >>= 1;
                                for (r = u; r < i; r += c) o[r] = a << 16 | l;
                                ++s
                            }
                    return [o, n]
                }, e.prototype.readBlock = function() {
                    function e(t, e, n, r, i) {
                        for (var o = t.getBits(n) + r; o-- > 0;) e[p++] = i
                    }
                    var a = this.getBits(3);
                    if (1 & a && (this.eof = !0), 0 != (a >>= 1)) {
                        var c, l;
                        if (1 == a) c = o, l = s;
                        else if (2 == a) {
                            for (var u = this.getBits(5) + 257, h = this.getBits(5) + 1, f = this.getBits(4) + 4, d = Array(n.length), p = 0; p < f;) d[n[p++]] = this.getBits(3);
                            for (var m = this.generateHuffmanTable(d), g = 0, p = 0, w = u + h, y = new Array(w); p < w;) {
                                var v = this.getCode(m);
                                16 == v ? e(this, y, 2, 3, g) : 17 == v ? e(this, y, 3, 3, g = 0) : 18 == v ? e(this, y, 7, 11, g = 0) : y[p++] = g = v
                            }
                            c = this.generateHuffmanTable(y.slice(0, u)), l = this.generateHuffmanTable(y.slice(u, w))
                        } else t("Unknown block type in flate stream");
                        for (var b = (O = this.buffer) ? O.length : 0, x = this.bufferLength;;) {
                            var k = this.getCode(c);
                            if (k < 256) x + 1 >= b && (b = (O = this.ensureBuffer(x + 1)).length), O[x++] = k;
                            else {
                                if (256 == k) return void(this.bufferLength = x);
                                var _ = (k = r[k -= 257]) >> 16;
                                _ > 0 && (_ = this.getBits(_));
                                g = (65535 & k) + _;
                                k = this.getCode(l), (_ = (k = i[k]) >> 16) > 0 && (_ = this.getBits(_));
                                var S = (65535 & k) + _;
                                x + g >= b && (b = (O = this.ensureBuffer(x + g)).length);
                                for (var C = 0; C < g; ++C, ++x) O[x] = O[x - S]
                            }
                        }
                    } else {
                        var A, q = this.bytes,
                            T = this.bytesPos;
                        void 0 === (A = q[T++]) && t("Bad block header in flate stream");
                        var P = A;
                        void 0 === (A = q[T++]) && t("Bad block header in flate stream"), P |= A << 8, void 0 === (A = q[T++]) && t("Bad block header in flate stream");
                        var I = A;
                        void 0 === (A = q[T++]) && t("Bad block header in flate stream"), (I |= A << 8) != (65535 & ~P) && t("Bad uncompressed block length in flate stream"), this.codeBuf = 0, this.codeSize = 0;
                        var E = this.bufferLength,
                            O = this.ensureBuffer(E + P),
                            B = E + P;
                        this.bufferLength = B;
                        for (var F = E; F < B; ++F) {
                            if (void 0 === (A = q[T++])) {
                                this.eof = !0;
                                break
                            }
                            O[F] = A
                        }
                        this.bytesPos = T
                    }
                }, e
            }
        }();
    return function(t) {
        var e = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
        void 0 === t.btoa && (t.btoa = function(t) {
            var n, r, i, o, a, s = 0,
                c = 0,
                l = "",
                u = [];
            if (!t) return t;
            do {
                n = (a = t.charCodeAt(s++) << 16 | t.charCodeAt(s++) << 8 | t.charCodeAt(s++)) >> 18 & 63, r = a >> 12 & 63, i = a >> 6 & 63, o = 63 & a, u[c++] = e.charAt(n) + e.charAt(r) + e.charAt(i) + e.charAt(o)
            } while (s < t.length);
            l = u.join("");
            var h = t.length % 3;
            return (h ? l.slice(0, h - 3) : l) + "===".slice(h || 3)
        }), void 0 === t.atob && (t.atob = function(t) {
            var n, r, i, o, a, s, c = 0,
                l = 0,
                u = [];
            if (!t) return t;
            t += "";
            do {
                n = (s = e.indexOf(t.charAt(c++)) << 18 | e.indexOf(t.charAt(c++)) << 12 | (o = e.indexOf(t.charAt(c++))) << 6 | (a = e.indexOf(t.charAt(c++)))) >> 16 & 255, r = s >> 8 & 255, i = 255 & s, u[l++] = 64 == o ? String.fromCharCode(n) : 64 == a ? String.fromCharCode(n, r) : String.fromCharCode(n, r, i)
            } while (c < t.length);
            return u.join("")
        }), Array.prototype.map || (Array.prototype.map = function(t) {
            if (void 0 === this || null === this || "function" != typeof t) throw new TypeError;
            for (var e = Object(this), n = e.length >>> 0, r = new Array(n), i = arguments.length > 1 ? arguments[1] : void 0, o = 0; o < n; o++) o in e && (r[o] = t.call(i, e[o], o, e));
            return r
        }), Array.isArray || (Array.isArray = function(t) {
            return "[object Array]" === Object.prototype.toString.call(t)
        }), Array.prototype.forEach || (Array.prototype.forEach = function(t, e) {
            if (void 0 === this || null === this || "function" != typeof t) throw new TypeError;
            for (var n = Object(this), r = n.length >>> 0, i = 0; i < r; i++) i in n && t.call(e, n[i], i, n)
        }), Object.keys || (Object.keys = function() {
            var t = Object.prototype.hasOwnProperty,
                e = !{
                    toString: null
                }.propertyIsEnumerable("toString"),
                n = ["toString", "toLocaleString", "valueOf", "hasOwnProperty", "isPrototypeOf", "propertyIsEnumerable", "constructor"],
                r = n.length;
            return function(i) {
                if ("object" != typeof i && ("function" != typeof i || null === i)) throw new TypeError;
                var o, a, s = [];
                for (o in i) t.call(i, o) && s.push(o);
                if (e)
                    for (a = 0; a < r; a++) t.call(i, n[a]) && s.push(n[a]);
                return s
            }
        }()), String.prototype.trim || (String.prototype.trim = function() {
            return this.replace(/^\s+|\s+$/g, "")
        }), String.prototype.trimLeft || (String.prototype.trimLeft = function() {
            return this.replace(/^\s+/g, "")
        }), String.prototype.trimRight || (String.prototype.trimRight = function() {
            return this.replace(/\s+$/g, "")
        })
    }("undefined" != typeof self && self || "undefined" != typeof window && window || void 0), e
});