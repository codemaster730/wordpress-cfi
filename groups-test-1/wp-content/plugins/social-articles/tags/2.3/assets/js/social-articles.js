
function getMoreArticles(){
     jQuery("#more-articles-button").hide();
     jQuery("#more-articles-loader").show();

     jQuery.ajax({
            type: 'post',
            url: MyAjax.ajaxurl,
            data: { action: "get_more_articles", offset:jQuery("#offset").val(), status:"publish"},                                                
            success:
            function(response) {    
               jQuery("#more-articles-loader").hide();

               newOffset =parseInt(jQuery("#offset").val()) + parseInt(jQuery("#inicialcount").val());
               if(newOffset >= parseInt(jQuery("#postcount").val())){
                   jQuery(".more-articles-button-container").hide();                               
               }else{
                   jQuery("#offset").val(newOffset);  
               }                    
               jQuery("#more-container-publish").append(response);
                jQuery("#more-articles-button").show();
            }
     });         
}

function deleteArticle(postId){
    jQuery("#delete-"+postId).addClass("deleting");          
	jQuery.ajax({
	            type: 'post',
	            url: MyAjax.ajaxurl,
	            data: { action: "delete_article", post_id:postId},                                                
	            success:
	            function(response) {
                   response = JSON.parse(response);
                   if(response.status == 'ok'){

                       jQuery("#"+response.post_status+" span").html(parseInt(jQuery("#"+response.post_status+" span").html())-1);
                       counterId = '#'+jQuery("#current-state").val() + '-count';
                       jQuery(counterId).html(parseInt(jQuery(counterId).html())-1);
                       jQuery("#"+postId).hide();
                   }
	            }
	      });
	 
}

function submitForm(){
    if (typeof SAPvalidateFields == 'function') {
        SAPvalidateFields();
    }else{
        jQuery('#post-maker-container').hide(); jQuery('.saving-message').show();
    }

}