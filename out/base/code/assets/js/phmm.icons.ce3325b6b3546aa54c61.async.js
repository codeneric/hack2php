webpackJsonp([10],{"2klg":function(e,t,n){"use strict";(function(e){var r=n("GiK3"),a=(n.n(r),n("RH2O")),o=n("nz+8"),l=n.n(o),i=n("xH95"),c=n.n(i),u=n("WiM9"),d=(n.n(u),n("Wgvh")),s=n.n(d),f=(n("oMDR"),n("zxJa"),n("DO2Q")),m=this;t.a=Object(a.b)(function(e,t){return{display:e.configuration.downloadable_single,image:e.gallery.loaded[t.imageID],ajax_url:e.globals.ajax_url,project_id:e.projectGlobals.id}},function(e,t){return{onClick:(n=regeneratorRuntime.mark(function e(t,n,r){var a,o;return regeneratorRuntime.wrap(function(e){for(;;)switch(e.prev=e.next){case 0:if(t){e.next=2;break}throw new Error("Single Image Download Failed. Image not defined");case 2:return e.prev=3,e.next=6,Object(f.a)({ajax_url:n,action:"phmm_get_original_image_url",payload:{image_id:t.id,project_id:r}});case 6:if(!((a=e.sent)&&a.body&&a.body.data&&a.body.data.data&&a.body.data.data.url)){e.next=12;break}o=a.body.data.data.url,s()([o]),e.next=13;break;case 12:throw new Error("Single Image Download Failed. Request malformed");case 13:e.next=18;break;case 15:throw e.prev=15,e.t0=e.catch(3),e.t0;case 18:case"end":return e.stop()}},e,m,[[3,15]])}),r=function(){var e=n.apply(this,arguments);return new Promise(function(t,n){return function r(a,o){try{var l=e[a](o),i=l.value}catch(e){return void n(e)}if(!l.done)return Promise.resolve(i).then(function(e){r("next",e)},function(e){r("throw",e)});t(i)}("next")})},function(e,t,n){return r.apply(this,arguments)})};var n,r})(Object(u.withTheme)()(function(t){t.transparent;var n=t.theme,r=t.onClick,a=(t.isProofed,t.image),o=t.ajax_url,i=t.project_id;if(!t.display)return null;var u=e.createElement(c.a,null);return e.createElement(l.a,{className:"phmm-single-download-image-btn",onClick:function(e){function t(){return e.apply(this,arguments)}return t.toString=function(){return e.toString()},t}(function(){return r(a,o,i)}),style:{margin:"10px",background:"dark"!==n.palette.type?"white":"inherit",borderRadius:"50%"}},u)}))}).call(t,n("uiaF"))},"3SrW":function(e,t,n){"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.styles=void 0;var r=d(n("Dd8w")),a=d(n("bOdI")),o=d(n("+6Bu")),l=d(n("GiK3")),i=(d(n("GjON")),d(n("HW6M"))),c=d(n("3rUI")),u=n("Sfvb");function d(e){return e&&e.__esModule?e:{default:e}}var s=t.styles=function(e){return{root:{userSelect:"none"},colorAccent:{color:e.palette.secondary.light},colorAction:{color:e.palette.action.active},colorContrast:{color:e.palette.primary.contrastText},colorDisabled:{color:e.palette.action.disabled},colorError:{color:e.palette.error.main},colorPrimary:{color:e.palette.primary.main}}};function f(e){var t=e.children,n=e.classes,c=e.className,d=e.color,s=(0,o.default)(e,["children","classes","className","color"]),f=(0,i.default)("material-icons",n.root,(0,a.default)({},n["color"+(0,u.capitalizeFirstLetter)(d)],"inherit"!==d),c);return l.default.createElement("span",(0,r.default)({className:f,"aria-hidden":"true"},s),t)}f.propTypes={},f.defaultProps={color:"inherit"},f.muiName="Icon",t.default=(0,c.default)(s,{name:"MuiIcon"})(f)},"6dUG":function(e,t,n){"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.styles=void 0;var r=m(n("Dd8w")),a=m(n("bOdI")),o=m(n("+6Bu")),l=m(n("GiK3")),i=(m(n("GjON")),m(n("HW6M"))),c=m(n("3rUI")),u=m(n("VopY")),d=n("Sfvb"),s=m(n("nGxd")),f=n("ZY1E");function m(e){return e&&e.__esModule?e:{default:e}}n("VJec");var p=t.styles=function(e){return{root:{textAlign:"center",flex:"0 0 auto",fontSize:e.typography.pxToRem(24),width:6*e.spacing.unit,height:6*e.spacing.unit,padding:0,borderRadius:"50%",color:e.palette.action.active,transition:e.transitions.create("background-color",{duration:e.transitions.duration.shortest})},colorAccent:{color:e.palette.secondary.light},colorContrast:{color:e.palette.primary.contrastText},colorPrimary:{color:e.palette.primary.main},colorInherit:{color:"inherit"},disabled:{color:e.palette.action.disabled},label:{width:"100%",display:"flex",alignItems:"inherit",justifyContent:"inherit"},icon:{width:"1em",height:"1em"},keyboardFocused:{backgroundColor:e.palette.text.divider}}};function g(e){var t,n=e.buttonRef,c=e.children,m=e.classes,p=e.className,g=e.color,h=e.disabled,v=e.rootRef,y=(0,o.default)(e,["buttonRef","children","classes","className","color","disabled","rootRef"]);return l.default.createElement(u.default,(0,r.default)({className:(0,i.default)(m.root,(t={},(0,a.default)(t,m["color"+(0,d.capitalizeFirstLetter)(g)],"default"!==g),(0,a.default)(t,m.disabled,h),t),p),centerRipple:!0,keyboardFocusedClassName:m.keyboardFocused,disabled:h,rootRef:n,ref:v},y),l.default.createElement("span",{className:m.label},"string"==typeof c?l.default.createElement(s.default,{className:m.icon},c):l.default.Children.map(c,function(e){return(0,f.isMuiElement)(e,["Icon","SvgIcon"])?l.default.cloneElement(e,{className:(0,i.default)(m.icon,e.props.className)}):e})))}g.propTypes={},g.defaultProps={color:"default",disabled:!1,disableRipple:!1},t.default=(0,c.default)(p,{name:"MuiIconButton"})(g)},BSVS:function(e,t,n){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var r=n("kZwz");Object.defineProperty(t,"default",{enumerable:!0,get:function(){return(e=r,e&&e.__esModule?e:{default:e}).default;var e}})},GumP:function(e,t,n){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var r=l(n("GiK3")),a=l(n("9rdB")),o=l(n("VJec"));function l(e){return e&&e.__esModule?e:{default:e}}var i=r.default.createElement("path",{d:"M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"}),c=function(e){return r.default.createElement(o.default,e,i)};(c=(0,a.default)(c)).muiName="SvgIcon",t.default=c},Wgvh:function(e,t,n){"use strict";function r(e){var t=document.createElement("a");t.download="",t.href=e,t.dispatchEvent(new MouseEvent("click"))}e.exports=function(e){if(!e)throw new Error("`urls` required");if(void 0===document.createElement("a").download)return function(e){var t=0;!function n(){var r=document.createElement("iframe");r.style.display="none",r.src=e[t++],document.documentElement.appendChild(r);var a=setInterval(function(){"complete"!==r.contentWindow.document.readyState&&"interactive"!==r.contentWindow.document.readyState||(clearInterval(a),setTimeout(function(){r.parentNode.removeChild(r)},1e3),t<e.length&&n())},100)}()}(e);var t=0;e.forEach(function(e){if(/Firefox\//i.test(navigator.userAgent)&&!function(e){var t=document.createElement("a");return t.href=e,location.hostname===t.hostname&&location.protocol===t.protocol}(e))return setTimeout(r.bind(null,e),100*++t);r(e)})}},ZY1E:function(e,t,n){"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.cloneChildrenWithClassName=function(e,t){return r.Children.map(e,function(e){return(0,r.isValidElement)(e)&&(0,r.cloneElement)(e,{className:e.props.hasOwnProperty("className")?e.props.className+" "+t:t})})},t.isMuiElement=function(e,t){return(0,r.isValidElement)(e)&&-1!==t.indexOf(e.type.muiName)},t.isMuiComponent=function(e,t){return-1!==t.indexOf(e.muiName)};var r=n("GiK3")},a4tG:function(e,t,n){"use strict";Object.defineProperty(t,"__esModule",{value:!0}),function(e){var r=n("GiK3"),a=(n.n(r),n("wF3A")),o=n.n(a),l=n("mFTE"),i=n("owsN"),c=n("2klg"),u=o()({loader:function(){return n.e(19).then(n.bind(null,"aZsD"))},loading:function(){return null}}),d=function(t){var n=t.className,r=t.containerWidth,a=(t.onButtonClick,t.isProofed,t.theme,t.styles),o=t.imageID,d=(t.hide,void 0);return d=r<=300?e.createElement(u,{imageID:o}):[e.createElement(c.a,{key:o+"-singledownload",imageID:o}),e.createElement(l.a,{key:o+"-commenticon",imageID:o}),e.createElement(i.a,{key:o+"-prooficon",imageID:o})],e.createElement("div",{className:n,style:a},d)};t.default=function(t){t.imageID;return e.createElement(d,t)}}.call(t,n("uiaF"))},kZwz:function(e,t,n){"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.styles=void 0;var r=d(n("Dd8w")),a=d(n("bOdI")),o=d(n("+6Bu")),l=d(n("GiK3")),i=(d(n("GjON")),d(n("HW6M"))),c=d(n("3rUI")),u=n("Sfvb");function d(e){return e&&e.__esModule?e:{default:e}}var s=t.styles=function(e){return{root:{position:"relative",display:"inline-flex",verticalAlign:"middle"},badge:{display:"flex",flexDirection:"row",flexWrap:"wrap",justifyContent:"center",alignContent:"center",alignItems:"center",position:"absolute",top:-12,right:-12,fontFamily:e.typography.fontFamily,fontWeight:e.typography.fontWeight,fontSize:e.typography.pxToRem(12),width:24,height:24,borderRadius:"50%",backgroundColor:e.palette.color,color:e.palette.textColor,zIndex:1},colorPrimary:{backgroundColor:e.palette.primary.main,color:e.palette.primary.contrastText},colorAccent:{backgroundColor:e.palette.secondary.light,color:e.palette.getContrastText(e.palette.secondary.light)}}};function f(e){var t=e.badgeContent,n=e.classes,c=e.className,d=e.color,s=e.children,f=(0,o.default)(e,["badgeContent","classes","className","color","children"]),m=(0,i.default)(n.badge,(0,a.default)({},n["color"+(0,u.capitalizeFirstLetter)(d)],"default"!==d));return l.default.createElement("span",(0,r.default)({className:(0,i.default)(n.root,c)},f),s,l.default.createElement("span",{className:m},t))}f.propTypes={},f.defaultProps={color:"default"},t.default=(0,c.default)(s,{name:"MuiBadge"})(f)},mFTE:function(e,t,n){"use strict";var r=n("GiK3"),a=(n.n(r),n("RH2O")),o=n("nz+8"),l=(n.n(o),n("oyeF")),i=(n.n(l),n("WiM9")),c=(n.n(i),n("Eys2")),u=n("BSVS");n.n(u),Object.assign;t.a=Object(a.b)(function(e,t){var n=e.comments.filter(function(e){return e.imageID===t.imageID&&"SENT"===e.status}).length;return 0===n&&(n=e.projectGlobals.initialCommentCount.filter(function(e){return e.image_id==t.imageID}).map(function(e){return e.count}).toString()||0),{display:e.configuration.commentable,commentCount:n}},function(e,t){return{onClick:function(){e(Object(c.b)(t.imageID))}}})(Object(i.withTheme)()(function(e){e.display,e.theme,e.onClick,e.commentCount,e.style;return null}))},nGxd:function(e,t,n){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var r=n("3SrW");Object.defineProperty(t,"default",{enumerable:!0,get:function(){return(e=r,e&&e.__esModule?e:{default:e}).default;var e}})},"nz+8":function(e,t,n){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var r=n("6dUG");Object.defineProperty(t,"default",{enumerable:!0,get:function(){return(e=r,e&&e.__esModule?e:{default:e}).default;var e}})},owsN:function(e,t,n){"use strict";(function(e){var r=n("GiK3"),a=(n.n(r),n("RH2O")),o=n("nz+8"),l=n.n(o),i=n("GumP"),c=n.n(i),u=n("WiM9"),d=(n.n(u),n("HW6M")),s=n.n(d),f=n("oMDR"),m=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var n=arguments[t];for(var r in n)Object.prototype.hasOwnProperty.call(n,r)&&(e[r]=n[r])}return e};t.a=Object(a.b)(function(e,t){return{isProofed:e.proofs.current.has(t.imageID),display:e.configuration.favoritable}},function(e,t){return{onClick:function(){e(Object(f.d)(t.imageID))}}})(Object(u.withTheme)()(function(t){t.transparent;var n=t.theme,r=t.onClick,a=t.isProofed,o=t.display,i=t.style,u=e.createElement(c.a,null);return o?e.createElement(l.a,{className:s()("phmm-proof-image-btn",{proofed:a}),color:a?"primary":"default",onClick:r,style:m({},i,{margin:"10px",background:"dark"!==n.palette.type?"white":"inherit",borderRadius:"50%"})},u):null}))}).call(t,n("uiaF"))},oyeF:function(e,t,n){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var r=l(n("GiK3")),a=l(n("9rdB")),o=l(n("VJec"));function l(e){return e&&e.__esModule?e:{default:e}}var i=r.default.createElement("path",{d:"M21.99 4c0-1.1-.89-2-1.99-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14l4 4-.01-18zM18 14H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"}),c=function(e){return r.default.createElement(o.default,e,i)};(c=(0,a.default)(c)).muiName="SvgIcon",t.default=c},xH95:function(e,t,n){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var r=l(n("GiK3")),a=l(n("9rdB")),o=l(n("VJec"));function l(e){return e&&e.__esModule?e:{default:e}}var i=r.default.createElement("path",{d:"M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"}),c=function(e){return r.default.createElement(o.default,e,i)};(c=(0,a.default)(c)).muiName="SvgIcon",t.default=c}});
//# sourceMappingURL=~/phmm.icons.ce3325b6b3546aa54c61.async.js.map