import{_ as r,a as n}from"../app.ca983809.js";import"./vue.c7926159.js";import{c as s,aM as m,o as u}from"./@vue.21d3b485.js";import"./vue-codemirror.f710c5d3.js";import"./codemirror.65c2c8aa.js";import"./@codemirror.86619810.js";import"./@lezer.13b71bd8.js";import"./crelt.2ca95e67.js";import"./style-mod.dd020e09.js";import"./w3c-keyname.f0bedd1c.js";import"./vuedraggable.6704fbca.js";import"./axios.49d3df40.js";import"./sortablejs.8b43ee28.js";import"./lodash.009183e4.js";import"./@kyvg.9e3dee5d.js";import"./vue-router.5d347134.js";import"./vuex.7e60e04e.js";const c={name:"DashboardPage",components:{Layout:n},props:{currentRoute:Object},data(){return{data:null,meta:null,layout:null}},created(){this.$emit("action","setTab",{key:this._.vnode.key,meta:{title:""},loading:!0}),this.get()},methods:{action(){typeof this[arguments[0]]=="function"&&this[arguments[0]](...Array.from(arguments).splice(1))||this.$emit("action",...arguments)},get(){axios.get("api/dashboard",{params:this.currentRoute.query||null}).then(t=>{this.data=t.data.data,this.meta=t.data.meta,this.layout=t.data.layout,this.$emit("action","setTab",{key:this._.vnode.key,meta:{title:""},loading:!1})})},pagination(t){this.$emit("action","pushRouter","?"+t,()=>this.get(this.element))}}};function p(t,l,e,h,a,o){const i=m("layout");return u(),s(i,{data:a.data,meta:a.meta,layout:a.layout,onAction:o.action,"current-route":e.currentRoute},null,8,["data","meta","layout","onAction","current-route"])}const q=r(c,[["render",p]]);export{q as default};
