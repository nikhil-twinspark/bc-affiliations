<?php
function bc_affiliation_create_metabox() {
    add_meta_box(
        'bc_affiliation_metabox',
        'Affiliation',
        'bc_affiliation_metabox',
        'bc_affiliations',
        'normal',
        'default'
    );
}
add_action( 'add_meta_boxes', 'bc_affiliation_create_metabox' );

function bc_affiliation_metabox() {
global $post; // Get the current post data
$name = get_post_meta( $post->ID, 'affiliation_name', true );
$link = get_post_meta( $post->ID, 'affiliation_link', true );
$image = get_post_meta( $post->ID, 'affiliation_custom_image', true );
?>

<div class="container">
  <div class="form-group row">
    <label class="col-sm-2 col-form-label">Name</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" name="affiliation_name" id="affiliation_name" value="<?= $name?>" required>
    </div>
  </div>
  <div class="form-group row">
    <label class="col-sm-2 col-form-label">Link</label>
    <div class="col-sm-10">
      <input type="url" class="form-control" name="affiliation_link" id="affiliation_link" value="<?= $link?>" required>
    </div>
  </div>

  <div class="form-group row">
    <label class="col-sm-2 col-form-label">Icon</label>
    <div class="col-sm-10">
        <input type="text" name="affiliation_custom_image" id="" class="meta-image col-sm-2" value="<?= $image;?>" required accept='image/*'>
        <input type="button" class="button bc-affiliation-image-upload col-sm-3" value="Upload">

        <div class="image-preview col-sm-3" style="float: right;margin-right: 30%;">
            <?php if(isset($image) && !empty($image)){?>
            <img src="<?php echo $image;?>" class="rounded-circle" style="width: 90px; height: 90px;">
            <?php }else{?>
            <img src="http://placehold.it/150x150" class="rounded-circle" style="width: 90px; height: 90px;"/>
            <?php }?>
        </div>
    </div>
  </div>
  <!-- <div class="form-group row">
    <label class="col-sm-2 col-form-label"><b>Shortcode :</b></label>
    <div class="col-sm-10">
      [bc-affiliation id="<?= $post->ID?>"]
    </div>
  </div> -->
</div>

<?php
    wp_nonce_field( 'bc_affiliation_form_metabox_nonce', 'bc_affiliation_form_metabox_process' );
}

function bc_affiliation_save_metabox( $post_id, $post ) {
/*echo "<pre>";
print_r($_POST);
print_r($post);
echo "</pre>";
die('ss');*/
    if ( !isset( $_POST['bc_affiliation_form_metabox_process'] ) ) return;
    if ( !wp_verify_nonce( $_POST['bc_affiliation_form_metabox_process'], 'bc_affiliation_form_metabox_nonce' ) ) {
        return $post->ID;
    }
    if ( !current_user_can( 'edit_post', $post->ID )) {
        return $post->ID;
    }
    if ( !isset( $_POST['affiliation_name'] ) ) {
        return $post->ID;
    }
    if ( !isset( $_POST['affiliation_link'] ) ) {
        return $post->ID;
    }
    if ( !isset( $_POST['affiliation_custom_image'] ) ) {
        return $post->ID;
    }

    $sanitizedname = wp_filter_post_kses( $_POST['affiliation_name'] );
    $sanitizedtitle = wp_filter_post_kses( $_POST['affiliation_link'] );
    $sanitizedcustomimage = wp_filter_post_kses( $_POST['affiliation_custom_image'] );

    update_post_meta( $post->ID, 'affiliation_name', $sanitizedname );
    update_post_meta( $post->ID, 'affiliation_link', $sanitizedtitle );
    update_post_meta( $post->ID, 'affiliation_custom_image', $sanitizedcustomimage );
}
add_action( 'save_post', 'bc_affiliation_save_metabox', 1, 2 );

// Change Title on insert and update of location title
add_filter('wp_insert_post_data', 'bc_affiliation_change_title');
function bc_affiliation_change_title($data){
    if($data['post_type'] != 'bc_affiliations'){
        return $data;
    }
    if ( !isset( $_POST['affiliation_name'] ) ) {
        return $data;
    }
    $data['post_title'] = $_POST['affiliation_name'];
    return $data;
}
