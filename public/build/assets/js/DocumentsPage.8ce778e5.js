import{_ as n,a as s}from"../app.2d7615bf.js";import"./vue.c7926159.js";import{c as m,aM as u,o as c}from"./@vue.21d3b485.js";import"./vue-codemirror.f710c5d3.js";import"./codemirror.65c2c8aa.js";import"./@codemirror.86619810.js";import"./@lezer.13b71bd8.js";import"./crelt.2ca95e67.js";import"./style-mod.dd020e09.js";import"./w3c-keyname.f0bedd1c.js";import"./vuedraggable.6704fbca.js";import"./axios.49d3df40.js";import"./sortablejs.8b43ee28.js";import"./lodash.009183e4.js";import"./@kyvg.9e3dee5d.js";import"./vue-router.5d347134.js";import"./vuex.7e60e04e.js";const l={name:"DocumentsPage",components:{Layout:s},props:{currentRoute:Object},data(){return{data:null,meta:null,layout:null}},computed:{id(){return this.meta?.id||this.currentRoute.params&&this.currentRoute.params.id||null},title(){return this.meta?.pagetitle||this.meta?.id&&"..."||this.$root.lang("new_resource")}},created(){this.$emit("action","setTab",{key:this._.vnode.key,meta:{title:"..."},changed:!1,loading:!0}),this.get()},methods:{action(){typeof this[arguments[0]]=="function"?this[arguments[0]](...Array.from(arguments).splice(1)):this.$emit("action",...arguments)},get(){this.data=null,axios.get("api/documents/"+this.id,{params:this.currentRoute.query||{}}).then(t=>{this.data=t.data.data,this.meta=t.data.meta,this.layout=t.data.layout,this.$emit("action","setTab",{key:this._.vnode.key,meta:{title:this.title},changed:!1,loading:!1})})},filter(t){(!t||t.length>1)&&this.$emit("action","pushRouter",t?"?filter="+t:"",()=>this.get(this.element))},pagination(t){this.$emit("action","pushRouter","?"+t,()=>this.get(this.element))},sort(t,a){this.$emit("action","pushRouter","?order="+t+"&dir="+a,()=>this.get(this.element))}}};function h(t,a,i,p,e,o){const r=u("layout");return c(),m(r,{data:e.data,meta:e.meta,layout:e.layout,onAction:o.action,"current-route":i.currentRoute},null,8,["data","meta","layout","onAction","current-route"])}const q=n(l,[["render",h]]);export{q as default};