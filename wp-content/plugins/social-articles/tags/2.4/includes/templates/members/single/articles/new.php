<?php
global $post, $wpdb, $bp, $socialArticles;

$directWorkflow = isDirectWorkflow();

$statusLabels = array("publish"=>__('Published', 'social-articles'),
    "draft"=>__('Draft', 'social-articles'),
    "pending"=>__('Under review', 'social-articles'),
    "new-post"=>__('New', 'social-articles'));


$article_id = empty($_GET['article']) ? 0 : $_GET['article']; //Used by all fields
$article = new SA_Form($article_id);

$error_message = '';
$response = array();
$response['status'] = false;

if('POST' == $_SERVER['REQUEST_METHOD']){    
    $response = $article->save($_POST);
    if(!$response['status']){
        $error_message = '<div class="sa-error-container">';
        foreach ($response['messages'] as $message){
            $error_message .= '<p>'.$message.'</p>';
        }
        $error_message .= '</div>';
    };
}
?>

<?php if($response['status'] == true): ?>
<div class="post-save-options messages-container">
    <label id="save-message"><?php echo $response['saved_data']['message']; ?></label>
    <input type="submit" onclick="window.open('<?php echo $response['saved_data']['editarticle'];?>', '_self');" id="edit-article" class="button" value="<?php _e("Edit article", "social-articles"); ?>" />
    <input type="submit" onclick="window.open('<?php echo $response['saved_data']['viewarticle'];?>', '_self');"id="view-article" class="button" value="<?php _e("View article", "social-articles"); ?>" />
    <input type="submit" onclick="window.open('<?php echo $response['saved_data']['newarticle'];?>', '_self');"id="new-article" class="button" value="<?php _e("New article", "social-articles"); ?>" />
</div>
<?php else: ?>
<div class="saving-message" style="display: none; text-align: center">
    <p><?php _e('Saving your article. Please wait.', 'social-articles');?></p>
    <p><img src="<?php echo SA_BASE_URL;?>/assets/images/loading.svg" width="60"></p>
</div>
<div id="post-maker-container">
    <form action="" method="post" enctype="multipart/form-data" data-parsley-validate="">
        <?php echo $error_message; ?>
        <?php $article->show_article_status(); ?>
        <?php $article->show_fields(); ?>
        <div class="buttons-container" id="create-controls">
            <?php $article->show_publish_actions();?>
            <input type="submit"  value="<?php _e("Save", "social-articles"); ?>" onclick="submitForm()" />
            <input type="submit" class="button cancel" value="<?php _e("Cancel", "social-articles"); ?>" onclick="window.open('<?php echo $bp->loggedin_user->domain.'articles';?>', '_self'); return false;" />
        </div>
    </form>
</div>
<?php endif; ?>

