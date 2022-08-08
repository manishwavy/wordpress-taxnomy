<?php
/*
     Template Name: Product Listing
*/
?>
<?php
if(isset($_GET['sector']))
{
    $sector_param = $_GET['sector'];
    $array_value_sector = explode(",",$sector_param);
    if($array_value_sector[0] == '')
    {
        $count_c = '';
    }
    else{
        $count_c = count($array_value_sector);
    }
}
else{
    $count_c = '';
}


$categories_param = $_GET['categories'];
$array_value_categories = explode(",",$categories_param);

//if selected particular sector
if($count_c < 2 && $count_c != '')
{

    $taxonomy = $array_value_sector[0]; 
    $terms = get_terms($taxonomy);
    $createArrayForSector = array(
        'taxonomy' => $array_value_sector[0],
        'field' => 'slug',
        'terms' => wp_list_pluck($terms,'slug')
    );
    if(isset($_GET['brand']) && $_GET['brand'] != '')
    {

        $brand_param = $_GET['brand'];
        $array_value_brand = explode(",",$brand_param);
        $createArrayForBrand = array(
            'taxonomy' => $array_value_sector[0],
            'field' => 'slug',
            'terms' => $array_value_brand
        );
        $relationShip = 'AND';
    }
    else{
        $createArrayForBrand = '';
        $relationShip = 'AND';
    }
  //categories
  if(isset($_GET['categories']) && $_GET['categories'] != '')
  {
      $categories_param = $_GET['categories'];
      $array_value_categories = explode(",",$categories_param);
      $createArrayForCategories = array(
          'taxonomy' =>'product_categories',
          'field' => 'slug',
          'terms' => $array_value_categories,
      );
      $relationShip = 'OR';
  }
  else{
      $createArrayForCategories = '';
      $relationShip = 'OR';
  }
}
//if selected both or empty sector
else{
    $createArrayForSector ='';
    if(isset($_GET['brand']) && $_GET['brand'] != '')
    {

        $brand_param = $_GET['brand'];
        $array_value_brand = explode(",",$brand_param);
        $createArrayForBrand = array(
            'taxonomy' => 'education',
            'field' => 'slug',
            'terms' => $array_value_brand
        );
        $createArrayForBrandEdu = array(
            'taxonomy' => 'business',
            'field' => 'slug',
            'terms' => $array_value_brand
        );
        $relationShip = 'OR';
        
    }
    else{
        $createArrayForBrand = '';
        $relationShip = 'AND';
    }
       //categories
       if(isset($_GET['categories']) && $_GET['categories'] != '')
       {
           $categories_param = $_GET['categories'];
           $array_value_categories = explode(",",$categories_param);
           $createArrayForCategories = array(
               'taxonomy' =>'product_categories',
               'field' => 'slug',
               'terms' => $array_value_categories,
           );
       }
       else{
           $createArrayForCategories = '';
       }

}



$paged = get_query_var('paged') ? get_query_var('paged') : 1;
$args = array(
    'post_type' => 'product',
    'posts_per_page' => 12,
    'post_status'=> 'publish',
    'paged' => $paged,
    'tax_query' => array(
        'relation' => $relationShip,
     $createArrayForSector,
     $createArrayForBrand,
     $createArrayForBrandEdu,
     $createArrayForCategories
     )
    );
    $my_query = new WP_Query($args);



?>
<?php get_header();?>
<section class="product-listing">
    
    <div class="container-fluid">
        <div class="row px-md-3">
            <div class="col-lg-3">
                <div class="list-filter">
                    <p>FILTERS</p>
                </div>
                <div class="list list-sector mb-4">
                    <span class="select">
                    <h6>Sector</h6>
                    <i class="fa-solid fa-play rotate  <?php if(isset($_GET['sector']) && $_GET['sector'] != ''){ echo "down";} ?>"></i>
                    </span>
                    <ul class="mob-list-dropdown   <?php if(isset($_GET['sector']) && $_GET['sector'] != ''){ echo "blue";} ?>">
                    <?php
                    $taxonomies = get_object_taxonomies( 'product' );
                    foreach( $taxonomies as $taxo)
                    {
                        if($taxo != 'product_categories')
                        { 
                        ?>
                            <div class="">
                                <label class="form-check-label" for="check<?php echo $taxo; ?>">
                                <input type="checkbox" class="form-check-input filter-check" id="check<?php echo $taxo; ?>"  value="<?php echo $taxo; ?>" data-filter-type="sector"
                                <?php
                            if(in_array($taxo,$array_value_sector))
                            {
                                echo 'checked';
                            }
                            ?>
                                ><span  class="text-capitalize"><?php echo $taxo; ?></span>
                                </label>
                            </div> 
                        <?php
                        }
                    }
                    ?>
                    </ul>
                </div>
                <div class="list list-categories mb-4">
                    <span class="select">
                    <h6>Categories</h6>                    
                    <i class="fa-solid fa-play rotate  <?php if(isset($_GET['categories']) && $_GET['categories'] != ''){ echo "down";} ?>"></i>
                    </span>
                    <ul class="mob-list-dropdown  <?php if(isset($_GET['categories']) && $_GET['categories'] != ''){ echo "blue";} ?>">  
                        <?php
                        $args = array(
                                    'taxonomy' => 'product_categories',
                                    'orderby' => 'name',
                                    'order'   => 'ASC'
                                );
                        $cats = get_categories($args);
                        foreach($cats as $cat) {
                        ?>
                        <div class="form-check">
                        <label class="form-check-label" for="check<?php echo $cat->slug; ?>">
                            <input type="checkbox" class="form-check-input filter-check" id="check<?php echo $cat->slug; ?>"  value="<?php echo $cat->slug; ?>" data-filter-type="categories" 
                            
                            <?php
                            if(in_array($cat->slug,$array_value_categories))
                            {
                                echo 'checked';
                            }
                            ?>

                            > <?php echo $cat->name; ?>
                        </label>
                        </div>  

                        <?php
                        }
                        ?>
                    </ul>
                </div>
                <div class="list">
                    <span class="select">
                    <h6>Brand</h6>                    
                    <i class="fa-solid fa-play rotate <?php if(isset($_GET['brand']) && $_GET['brand'] != ''){ echo "down";} ?>"></i>
                    </span>
                
				    <ul class="mob-list-dropdown <?php if(isset($_GET['brand']) && $_GET['brand'] != ''){ echo "blue";} ?>">                    
					<?php
                    $taxonomy_business = 'business';
                    $tax_terms_business = get_terms($taxonomy_business, array('hide_empty' => false, 'parent' => 0));
                    $array_common_business = [];
					foreach($tax_terms_business as $term_single_business) {      
                       $array_data_business =  array(
                        "name"=>$term_single_business->name,
                        "slug"=>$term_single_business->slug
                       );
                       array_push($array_common_business,$array_data_business);
                    } 
                    $taxonomy_education = 'education';
                    $tax_terms_education = get_terms($taxonomy_education, array('hide_empty' => false, 'parent' => 0));
                    $array_common_education = [];
					foreach($tax_terms_education as $term_single_education) {      
                       $array_data_education =  array(
                        "name"=>$term_single_education->name,
                        "slug"=>$term_single_education->slug
                       );
                       array_push($array_common_education,$array_data_education);
                    } 
                    $all_array = array_merge($array_common_business,$array_common_education);
                    
                    $input_all = array_map("unserialize", array_unique(array_map("serialize", $all_array)));
                    foreach($input_all as $category)
                    {
                        
                        ?>
                        <div class="form-check">
                          <label class="form-check-label" for="check<?php echo $category['slug']; ?>">
                            <input type="checkbox" class="form-check-input filter-check" id="check<?php echo $category['slug']; ?>"  value="<?php echo $category['slug']; ?>" data-filter-type="brand"
                            <?php
                            if(in_array($category['slug'],$array_value_brand))
                            {
                                echo 'checked';
                            }
                            ?>
                            ><?php echo $category['name']; ?>
                          </label>
                        </div>  
                        <?php
                    }
					?>
                   
				    </ul>	

                </div>
            </div>
            <div class="col-lg-9">
                <div class="all-product px-5">
                    <div class="row">
                        <?php
                    if($my_query->have_posts()) {
                        while ($my_query->have_posts()) : $my_query->the_post();
                    ?>
                        <div class="col-xl-4 col-lg-6 col-md-4 col-sm-6 product-col">
                            <div class="product text-center">
                                <a href="<?php the_permalink(); ?>"><h3><?php the_title(); ?></h3></a>
                                <div class="product-img-inner">
                                    <a href="<?php the_permalink(); ?>">
                                    <?php if(has_post_thumbnail()){ ?>
                                        <img src="<?php   echo the_post_thumbnail_url( 'medium' ); ?>" class="img-fluid" alt="<?php the_title(); ?>" />
                                    <?php }else{ ?>
                                        <img src="<?php echo site_url(); ?>/wp-content/uploads/2022/07/placeholder.png" class="img-fluid" alt="<?php the_title(); ?>" />
                                    <?php } ?>
                                    </a>
                                </div>
                                <a href="<?php the_permalink(); ?>" class="btn btn-primary"> Learn More</a>
                            </div>
                        </div>
                    <?php
                    endwhile;
                }
                else{
                    ?>
                    <div class="col-md-12"><h3 class="text-center">No product found</h3></div>
                    <?php
                }
                    $total_pages = $my_query->max_num_pages;
                    wp_reset_postdata();
                    ?>
                    </div>
                </div>
            </div>
            <div class="col-12 d-lg-flex justify-content-lg-center pegition mt-5">
            <div class="d-lg-flex justify-content-lg-evenly ">
                <nav aria-label="Page navigation example pegination-blog ">
                    <ul class="pagination justify-content-center">
                    <?php
                        $big = 999999999;
                        echo paginate_links( array(
                            'base' => str_replace( $big, '%#%', get_pagenum_link( $big ) ),
                            'format' => '?paged=%#%',
                            'current' => max( 1, get_query_var('paged') ),
                            'total' => $total_pages,
                            'prev_text'    => __('<'),
                            'next_text'    => __('>'),
                        )); 
                    ?>
                    </ul>
                </nav>
            </div>
        </div>
        </div>
	



    </div>
</div>
</section>










<?php get_footer();?>
