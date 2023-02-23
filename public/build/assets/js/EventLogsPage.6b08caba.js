import{_ as d,A as h,T as g,P as _}from"../app.ca983809.js";import"./vue.c7926159.js";import{a as l,ad as a,e as f,c as y,aM as n,o as s}from"./@vue.21d3b485.js";import"./vue-codemirror.f710c5d3.js";import"./codemirror.65c2c8aa.js";import"./@codemirror.86619810.js";import"./@lezer.13b71bd8.js";import"./crelt.2ca95e67.js";import"./style-mod.dd020e09.js";import"./w3c-keyname.f0bedd1c.js";import"./vuedraggable.6704fbca.js";import"./axios.49d3df40.js";import"./sortablejs.8b43ee28.js";import"./lodash.009183e4.js";import"./@kyvg.9e3dee5d.js";import"./vue-router.5d347134.js";import"./vuex.7e60e04e.js";const v={name:"EventLogsPage",components:{ActionsButtons:h,TitleComponent:g,Panel:_},props:{currentRoute:Object},data(){return{data:null,meta:null}},created(){this.$emit("action","setTab",{key:this._.vnode.key,changed:!1,loading:!0}),this.get()},methods:{action(){typeof this[arguments[0]]=="function"?this[arguments[0]](...Array.from(arguments).splice(1)):this.$emit("action",...arguments)},get(){this.data?.columns&&(this.data.data=null),this.data?.pagination&&(this.data.pagination=null),this.meta=null,axios.get("api/eventlog",{params:this.currentRoute.query||null}).then(t=>{this.data=t.data.data,this.meta=t.data.meta,this.$emit("action","setTab",{key:this._.vnode.key,changed:!1,loading:!1})})},filters(t,o){const e=Object.assign({},this.currentRoute.query||{});t!==""?e[o]=t:delete e[o],delete e.page,this.$emit("action","pushRouter",{query:e},()=>this.get(this.element))},pagination(t){this.$emit("action","pushRouter","?"+t,()=>this.get(this.element))},sort(t,o){const e=Object.assign({},this.currentRoute.query||{});e.order=t,e.dir=o,this.$emit("action","pushRouter",{query:e},()=>this.get(this.element))},clear(){confirm(this.$root.lang("confirm_delete_eventlog"))}}},b={class:"flex flex-col"},k={key:1,class:"flex items-center justify-center grow"};function R(t,o,e,$,i,r){const c=n("actions-buttons"),m=n("title-component"),u=n("panel"),p=n("loader");return s(),l("div",b,[a(c,{data:["delete"],lang:{delete:t.$root.lang("clear_log")},class:f({delete:"bg-rose-600 hover:bg-rose-700 text-white"}),onAction:r.action},null,8,["lang","onAction"]),a(m,{help:t.$root.lang("eventlog_msg")},null,8,["help"]),i.data?(s(),y(u,{key:0,data:i.data,onAction:r.action,"current-route":e.currentRoute,class:"py-4"},null,8,["data","onAction","current-route"])):(s(),l("div",k,[a(p)]))])}const F=d(v,[["render",R]]);export{F as default};
