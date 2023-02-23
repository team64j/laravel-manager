import{_ as u,P as m,c as h,a as _}from"../app.ca983809.js";import"./vue.c7926159.js";import{a as g,b7 as f,bq as b,f as e,ad as r,w,e as S,aM as c,o as y,U as v,aK as V,aI as F}from"./@vue.21d3b485.js";import"./vue-codemirror.f710c5d3.js";import"./codemirror.65c2c8aa.js";import"./@codemirror.86619810.js";import"./@lezer.13b71bd8.js";import"./crelt.2ca95e67.js";import"./style-mod.dd020e09.js";import"./w3c-keyname.f0bedd1c.js";import"./vuedraggable.6704fbca.js";import"./axios.49d3df40.js";import"./sortablejs.8b43ee28.js";import"./lodash.009183e4.js";import"./@kyvg.9e3dee5d.js";import"./vue-router.5d347134.js";import"./vuex.7e60e04e.js";const x={name:"FilesPage",components:{Panel:m,Tree:h,Layout:_},props:{currentRoute:{type:Object},modelValue:{type:[null,Object,String,Number,Boolean],default:null}},data(){return this.keyView="CMS.PANEL.FILES.VIEW",this.keySidebar="CMS.PANEL.FILES.SIDEBAR",{data:null,meta:null,layout:null,panelView:this.$store.getters["Storage/get"]("panelFilesView","panel-list"),panelSidebar:this.$store.getters["Storage/get"]("panelFilesSidebar",!0)}},created(){this.get()},watch:{"currentRoute.params.id"(t,s){this.currentRoute.name!=="Files"||this.currentRoute.name==="Files"&&(t===s||t===this.data?.path)||this.get()}},methods:{action(){typeof this[arguments[0]]=="function"?this[arguments[0]](...Array.from(arguments).splice(1)):this.$emit("action",...arguments)},get(){this.data=null,axios.get("api/files"+(this.currentRoute?.params?.id?"/"+this.currentRoute.params.id:""),{params:this.currentRoute.query??{}}).then(t=>{this.data=t.data.data,this.meta=t.data.meta,this.layout=t.data.layout}).finally(()=>{this.$emit("action","setTab",{key:this._.vnode.key,changed:!1,loading:!1})})},toggleSidebar(){this.panelSidebar=!this.panelSidebar,this.$store.dispatch("Storage/set",{panelFilesSidebar:this.panelSidebar})},toggleViewIcons(){this.$refs.panel.$el.classList.remove("panel-list"),this.$refs.panel.$el.classList.add("panel-icons"),this.$store.dispatch("Storage/set",{panelFilesView:"panel-icons"})},toggleViewList(){this.$refs.panel.$el.classList.remove("panel-icons"),this.$refs.panel.$el.classList.add("panel-list"),this.$store.dispatch("Storage/set",{panelFilesView:"panel-list"})}}},o=t=>(V("data-v-32fc8eeb"),t=t(),F(),t),I={class:"flex flex-nowrap overflow-hidden h-full"},L={class:"files-tree grow-0 shrink-0 border-r"},R={class:"files-main"},k={class:"files-data"},A={class:"files-header"},C=o(()=>e("i",{class:"fa fa-bars w-5"},null,-1)),P=[C],$=o(()=>e("i",{class:"fa fa-th w-5"},null,-1)),E=[$],B=o(()=>e("i",{class:"fa fa-list w-5"},null,-1)),N=[B],D={type:"button",class:"btn-sm btn-green mx-0.5"};function M(t,s,l,j,n,a){const d=c("tree"),p=c("panel");return y(),g("div",I,[f(e("div",L,[r(d,{url:"api/files/tree",id:"Files",route:"Files",onAction:a.action,"current-route":l.currentRoute},null,8,["onAction","current-route"])],512),[[b,n.panelSidebar]]),e("div",R,[e("div",k,[r(p,{ref:"panel",data:{...n.data},"current-route":l.currentRoute,class:S(["pb-4",n.panelView]),onAction:a.action},{header:w(()=>[e("div",A,[e("div",null,[e("button",{type:"button",class:"btn-sm btn-gray mx-0.5",onClick:s[0]||(s[0]=(...i)=>a.toggleSidebar&&a.toggleSidebar(...i))},P)]),e("div",null,[e("button",{type:"button",class:"btn-view btn-view-icons btn-sm btn-gray mx-0.5",onClick:s[1]||(s[1]=(...i)=>a.toggleViewIcons&&a.toggleViewIcons(...i))},E),e("button",{type:"button",class:"btn-view btn-view-list btn-sm btn-gray mx-0.5",onClick:s[2]||(s[2]=(...i)=>a.toggleViewList&&a.toggleViewList(...i))},N),e("button",D,v(t.$root.lang("files_uploadfile")),1)])])]),_:1},8,["data","current-route","class","onAction"])])])])}const ae=u(x,[["render",M],["__scopeId","data-v-32fc8eeb"]]);export{ae as default};
