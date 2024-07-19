"use strict";(self.webpackChunknextcloud=self.webpackChunknextcloud||[]).push([[4589],{7869:(t,e,n)=>{n.d(e,{A:()=>l});var a=n(71354),i=n.n(a),s=n(76314),o=n.n(s)()(i());o.push([t.id,".public-auth-prompt__subtitle{font-size:16px;margin-block:12px}.public-auth-prompt__header{margin-block:12px}.public-auth-prompt__form{margin-block:24px}","",{version:3,sources:["webpack://./apps/files_sharing/src/views/PublicAuthPrompt.vue"],names:[],mappings:"AAEC,8BAEC,cAAA,CACA,iBAAA,CAGD,4BAEC,iBAAA,CAGD,0BAEC,iBAAA",sourcesContent:["\n.public-auth-prompt {\n\t&__subtitle {\n\t\t// Smaller than dialog title\n\t\tfont-size: 16px;\n\t\tmargin-block: 12px;\n\t}\n\n\t&__header {\n\t\t// Fix extra margin generating an unwanted gap\n\t\tmargin-block: 12px;\n\t}\n\n\t&__form {\n\t\t// Double the margin of the header\n\t\tmargin-block: 24px;\n\t}\n}\n"],sourceRoot:""}]);const l=o},4589:(t,e,n)=>{n.r(e),n.d(e,{default:()=>v});var a=n(85471),i=n(53334),s=n(54332),o=n(94219),l=n(52201),r=n(82182),p=n(32981);const u=(0,a.pM)({name:"PublicAuthPrompt",components:{NcButton:s.A,NcDialog:o.A,NcNoteCard:l.A,NcTextField:r.A},setup:()=>({t:i.t,owner:(0,p.C)("files_sharing","owner",""),ownerDisplayName:(0,p.C)("files_sharing","ownerDisplayName",""),label:(0,p.C)("files_sharing","label",""),note:(0,p.C)("files_sharing","note",""),filename:(0,p.C)("files_sharing","filename","")}),data:()=>({name:""}),computed:{dialogName(){return this.t("files_sharing","Upload files to {folder}",{folder:this.label||this.filename})}},beforeMount(){const t=localStorage.getItem("nick");t&&(this.name=t)},methods:{onSubmit(){const t=this.$refs.form;t.checkValidity()?""!==this.name.trim()&&(localStorage.setItem("nick",this.name),this.$emit("close")):t.reportValidity()}}});var c=n(85072),m=n.n(c),h=n(97825),d=n.n(h),_=n(77659),f=n.n(_),b=n(55056),g=n.n(b),A=n(10540),C=n.n(A),y=n(41113),x=n.n(y),N=n(7869),k={};k.styleTagTransform=x(),k.setAttributes=g(),k.insert=f().bind(null,"head"),k.domAPI=d(),k.insertStyleElement=C(),m()(N.A,k),N.A&&N.A.locals&&N.A.locals;const v=(0,n(14486).A)(u,(function(){var t=this,e=t._self._c;return t._self._setupProxy,e("NcDialog",{staticClass:"public-auth-prompt",attrs:{"dialog-classes":"public-auth-prompt__dialog","can-close":!1,name:t.dialogName},scopedSlots:t._u([{key:"actions",fn:function(){return[e("NcButton",{ref:"submit",attrs:{disabled:""===t.name.trim()},on:{click:t.onSubmit}},[t._v("\n\t\t\t"+t._s(t.t("files_sharing","Submit name"))+"\n\t\t")])]},proxy:!0}])},[t.owner?e("h3",{staticClass:"public-auth-prompt__subtitle"},[t._v("\n\t\t"+t._s(t.t("files_sharing","{ownerDisplayName} shared a folder with you.",{ownerDisplayName:t.ownerDisplayName}))+"\n\t")]):t._e(),t._v(" "),e("NcNoteCard",{staticClass:"public-auth-prompt__header",attrs:{type:"info"}},[e("p",{staticClass:"public-auth-prompt__description",attrs:{id:"public-auth-prompt-dialog-description"}},[t._v("\n\t\t\t"+t._s(t.t("files_sharing","To upload files, you need to provide your name first."))+"\n\t\t")])]),t._v(" "),e("form",{ref:"form",staticClass:"public-auth-prompt__form",attrs:{"aria-describedby":"public-auth-prompt-dialog-description"},on:{submit:function(t){t.preventDefault(),t.stopPropagation()}}},[e("NcTextField",{ref:"input",staticClass:"public-auth-prompt__input",attrs:{label:t.t("files_sharing","Enter your name"),name:"name",required:!0,minlength:2,value:t.name},on:{"update:value":function(e){t.name=e}}})],1)],1)}),[],!1,null,null,null).exports}}]);
//# sourceMappingURL=4589-4589.js.map?v=f528d9600121156d9e2c