window.ampPostMetaBox=function(t){"use strict";const e={data:{canonical:!1,previewLink:"",enabled:!0,canSupport:!0,statusInputName:"",l10n:{ampPreviewBtnLabel:""}},toggleSpeed:200,previewBtnSelector:"#post-preview",ampPreviewBtnSelector:"#amp-post-preview",boot:function(a){e.data=a,t(document).ready((function(){e.statusRadioInputs=t('[name="'+e.data.statusInputName+'"]'),e.data.enabled&&!e.data.canonical&&e.addPreviewButton(),e.listen()}))},listen:function(){t(e.ampPreviewBtnSelector).on("click.amp-post-preview",(function(t){t.preventDefault(),e.onAmpPreviewButtonClick()})),e.statusRadioInputs.prop("disabled",!0),t('.edit-amp-status, [href="#amp_status"]').click((function(a){a.preventDefault(),e.statusRadioInputs.prop("disabled",!1),e.toggleAmpStatus(t(a.target))})),t('#submitpost input[type="submit"]').on("click",(function(){t(e.ampPreviewBtnSelector).addClass("disabled")}))},addPreviewButton:function(){const a=t(e.previewBtnSelector);a.clone().insertAfter(a).prop({href:e.data.previewLink,id:e.ampPreviewBtnSelector.replace("#","")}).text(e.data.l10n.ampPreviewBtnLabel).parent().addClass("has-amp-preview")},onAmpPreviewButtonClick:function(){const a=t("<input>").prop({type:"hidden",name:"amp-preview",value:"do-preview"}).insertAfter(e.ampPreviewBtnSelector);t(e.previewBtnSelector).click(),a.remove()},toggleAmpStatus:function(a){const n=t("#amp-status-select"),i=t(".edit-amp-status");let p=n.data("amp-status");a.hasClass("button-cancel")||(p=e.statusRadioInputs.filter(":checked").val());const s=t("#amp-status-"+p);i.fadeToggle(e.toggleSpeed,(function(){i.is(":visible")?i.focus():n.find('input[type="radio"]').first().focus()})),n.slideToggle(e.toggleSpeed),e.data.canSupport&&(n.data("amp-status",p),s.prop("checked",!0),t(".amp-status-text").text(s.next().text()))}};return e}(jQuery);