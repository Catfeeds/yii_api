/**
 *
 */
$(function(){

	$('.toggle-data').each(function(){
		var data = $(this).attr('toggle-data');
		if(data=='') return true;

	     if($(this).is(":selected") || $(this).is(":checked")){
			 change_event(this)
		 }
	});

	$('select').change(function(){
		$('.toggle-data').each(function(){
			var data = $(this).attr('toggle-data');
			if(data=='') return true;

			 if($(this).is(":selected") || $(this).is(":checked")){
				 change_event(this)
			 }
		});
	});

	//动态预览
	$('input[name="title"]').keyup(function(){
		$('.editing').find('.title').text($(this).val());
		$('.editing').find('input[name="MaterialNews[title][]"]').val($(this).val());
	});
	$('input[name="author"]').keyup(function(){
		$('.editing').find('.author').text($(this).val());
		$('.editing').find('input[name="MaterialNews[author][]"]').val($(this).val());
	});
	$('input[name="link"]').keyup(function(){
		$('.editing').find('.link').text($(this).val());
		$('.editing').find('input[name="MaterialNews[link][]"]').val($(this).val());
	});
	$('textarea[name="summary"]').keyup(function(){
		$('.editing').find('.summary').text($(this).val());
		$('.editing').find('input[name="MaterialNews[summary][]"]').val($(this).val());
	});
//	imageEditor.addListener("contentChange",function(){
//		$('.editing').find('textarea[name="content"]').val(imageEditor.getContent());
//	});
//	imageEditor.addListener("ready", function () {
//       initForm($('.edit_item').eq(0));
// 	});

	/*编辑*/
	$('.preview_area').on('click','.editBtn',function (){
		$(this).parents('.edit_item').addClass('editing');
		$(this).parents('.edit_item').siblings().removeClass('editing');
		var index=$(".editBtn").index($(this));
		if($(".editBtn").length==1){
			$('.picSize').text('900X500');
			$('.edit_area').css('margin-top',0);
		}else{
			if (index == 0){
				$('.picSize').text('900X500');
				$('.edit_area').css('margin-top',0);
			}else {
				$('.picSize').text('200X200');
				$('.edit_area').css('margin-top',(index)*110+120);
			}
			
		}
		initForm($(this).parents('.edit_item'));
	});
	
	/*删除*/
	$('.preview_area').on('click','.delBtn',function (){
		if(!confirm('确认删除？')){
			return false;
		}
	
		var item_id = $(this).parents('.edit_item').find('input[name="id"]').val();
		if(item_id){
			$.post("<?php echo U('del_material_by_id');?>",{id:item_id});
		}
	
		var deLength=$(".delBtn").length;
		var index=$(".delBtn").index($(this));
		var num= (parseInt( $('.edit_area').css('margin-top'))-120)/110;
		
		if (deLength>1){
			if (index == 0){
				if (num < 1){
				$('.edit_area').css('margin-top',0);
				}else if(num == 1){
					$('.edit_area').css('margin-top',230);
					initForm($('.edit_item').eq(num+1));
				}
				else {
					$('.edit_area').css('margin-top',(num-1)*110+120);
					initForm($('.edit_item').eq(num));
				}
			}else {
				if (index == (num-1)){
					$('.edit_area').css('margin-top',(num-1)*110+120);
					initForm($('.edit_item').eq(num-1));
				}else if (index > (num-1)){
					$('.edit_area').css('margin-top',(num)*110+120); 
					initForm($('.edit_item').eq(num));
				}else {
					$('.edit_area').css('margin-top',(num-1)*110+120); 
					initForm($('.edit_item').eq(num));
				}
			}
		}else {
			$('.edit_area').css('margin-top',0);
			initForm($('.edit_item').eq(0));
		}
		$(this).parents('.edit_item').remove();
		
	});

})


function addMsg(){
	
	var curCount = $('.edit_item').size();
	if(curCount > 8){
		alert('你最多只可以增加8条图文信息');
		return false;
	}
	$('.picSize').text('200X200');
	
	var addHtml = $('<div data-index="'+curCount+'" class="appmsg_sub_item edit_item">'+
                    '<p class="title"></p>'+
                    '<div class="main_img">'+
                        '<img src="../../img/no_cover_pic.png" data-coverid="0"/>'+
                    '</div>'+
                    '<input type="hidden" name="MaterialNews[title][]" placeholder="这是标题"/>'+
                    '<input type="hidden" name="MaterialNews[cover_id][]" value="0"/>'+
                    '<input type="hidden" name="MaterialNews[summary][]" placeholder="这是摘要描述"/>'+
                    '<input type="hidden" name="MaterialNews[author][]" placeholder="作者"/>'+
                    '<input type="hidden" name="MaterialNews[link][]" placeholder="外链"/>'+
                    '<textarea style="display:none" name="content"></textarea>'+
                    '<div class="hover_area"><a href="javascript:;" class="editBtn" >编辑</a><a href="javascript:;" class="delBtn" >删除</a></div>'+
                '</div>');
	addHtml.insertBefore($('.appmsg_edit_action'));
	
}

//初使化
function initForm(_item){
	var title = $(_item).find('input[name="MaterialNews[title][]"]').val();
	var author = $(_item).find('input[name="MaterialNews[author][]"]').val();
	var link = $(_item).find('input[name="MaterialNews[link][]"]').val();
	var summary = $(_item).find('input[name="MaterialNews[summary][]"]').val();
	var content = $(_item).find('textarea[name="MaterialNews[content][]"]').val();
	var src = $(_item).find('img').attr('src');
	$('input[name="title"]').val(title);
	$('input[name="author"]').val(author);
	$('input[name="link"]').val(link);
	$('textarea[name="summary"]').val(summary);
	if(!content)content=" ";
	/*if(content){
		imageEditor.setContent(content);
	}*/
	$('.upload-img-box').show().find('img').attr('src',src);
}