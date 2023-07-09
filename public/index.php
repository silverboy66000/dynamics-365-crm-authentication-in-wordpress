<?php

if (isset($_GET['action']))
{
    require ('../../../../wp-config.php');
}

global $wpdb;
global $wp_query;
global $product;
$product_updated_list_settings = $wpdb->prefix . 'product_updated_list_settings';
$product_permission_list_settings = $wpdb->prefix . 'product_permission_list_settings';
$product_target_list_log  = $wpdb->prefix . 'product_target_list_log';
$wp_postmeta  = $wpdb->prefix . 'postmeta';

$color=null;
$queryCategories=null;
$queryTitle=null;
$queryCode=null;

$sqlPricePermission = "SELECT * FROM  $product_permission_list_settings where canPriceUpdate='price'";
$resultsPricePermission= $wpdb->get_row($sqlPricePermission);

$sqlQuantityPermission = "SELECT * FROM  $product_permission_list_settings where canPriceUpdate='quantity'";
$resultsQuantityPermission= $wpdb->get_row($sqlQuantityPermission);

$user = wp_get_current_user();



$productArgs = array(
    'posts_per_page' => -1,
    'post_type'      => 'product',
    'post_status'    => 'publish',
);

$categoryArgs = array(
    'taxonomy'     => 'product_cat',
    'hide_empty'     => 0,

);


if ($_GET['title'] and $_GET['title']!='')
{
    $queryTitle=$_GET['title'];
    $productArgs=array_merge($productArgs,[
        's'=>$_GET['title']
    ]);
}

if ($_GET['code'] and $_GET['code']!='')
{
    $queryCode=$_GET['code'];

}

if ($_GET['category'] and $_GET['category']!='')
{
    $queryCategories=$_GET['category'];

    $tax_query[] = array(
        'taxonomy' => 'product_cat',
        'field'    => 'term_id',
        'terms'    => $_GET['category'],
    );

    $productArgs=array_merge($productArgs,[
        'tax_query'=>$tax_query
    ]);
}


$loopProducts = new WP_Query( $productArgs );
$all_categories = get_categories( $categoryArgs );

if (isset($_GET['action']) and $_GET['action']=='update')
{

    $user = wp_get_current_user();
    $userid=$user->ID;

    $table_name_postMetaList = $wpdb->prefix . 'postmeta';
    $table_name_product_logs_list_settings = $wpdb->prefix . 'product_logs_list_settings';

    if ($_POST['quantity']!=null)
    {
        if ($_POST['quantity']==0)
        {
            $_stock_status='outofstock';
        }
        else
        {
            $_stock_status='instock';
        }
        try {
            $wpdb->update($table_name_postMetaList, array(
                'meta_value' => $_POST['quantity'],
            ),
                array(
                    'meta_key' => '_stock',
                    'post_id' => $_POST['id'],
                )
            );
            $wpdb->update($table_name_postMetaList, array(
                'meta_value' => $_stock_status,
            ),
                array(
                    'meta_key'=>'_stock_status',
                    'post_id'=>$_POST['id'],
                )
            );
        }
        catch (e $ex)
        {
            print_r($ex);

        }
    }
    if ($_POST['price']!=null)
    {
        $wpdb->update($table_name_postMetaList, array(
            'meta_value' => $_POST['price'],
        ),
            array(
                'meta_key'=>'_price',
                'post_id'=>$_POST['id'],
            )
        );

        $wpdb->update($table_name_postMetaList, array(
            'meta_value' => $_POST['price'],
        ),
            array(
                'meta_key'=>'_regular_price',
                'post_id'=>$_POST['id'],
            )
        );
    }

    $wpdb->insert($table_name_product_logs_list_settings, array(
            'productId' => $_POST['id'],
            'userId' => $userid,
            'price' => $_POST['price'],
            'inventory' => $_POST['quantity'],
            'updateDate' => $_stock_status,
        )
    );
    print_r('true');

    exit();
}

?>
<!--<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">-->
<link rel="stylesheet" type="text/css" href="https://buy.morabishop.ir/wp-content/plugins/product-order-management/public/asset/css/responsive.dataTables.min.css">

<script type="text/javascript" language="javascript" src="https://buy.morabishop.ir/wp-content/plugins/product-order-management/public/asset/js/jquery-3.5.1.js"></script>
<script type="text/javascript" language="javascript" src="https://buy.morabishop.ir/wp-content/plugins/product-order-management/public/asset/js/jquery.dataTables.min.js"></script>
<!--<script type="text/javascript" language="javascript" src="https://buy.morabishop.ir/wp-content/plugins/product-order-management/public/asset/js/dataTables.responsive.min.js"></script>-->
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<style>
    table.dataTable.dtr-column > tbody > tr > td.dtr-control::before
    {
        left: 110% !important;
        font-size: large !important;
    }


</style>
<style>
    #textName {
        -webkit-text-stroke: 1px white;
    }
    /*#result{*/
    /*    display:none;*/
    /*}*/

    .loader {
        border: 4px solid #f3f3f3;
        border-radius: 50%;
        border-top: 4px solid #3498db;
        width: 25px;
        height: 25px;
        -webkit-animation: spin 2s linear infinite; /* Safari */
        animation: spin 2s linear infinite;
    }

    /* Safari */
    @-webkit-keyframes spin {
        0% { -webkit-transform: rotate(0deg); }
        100% { -webkit-transform: rotate(360deg); }
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .hesab-box .home-sheba {
        display: none;
        text-align: center;
        font-size: 16px;
        padding: 10px;
        background: #ffd4d4;
        border: 1px solid #d9a3a3;
        border-radius: 5px;
    }
    @media screen and (max-width: 450px) {
        .priority-1{
            display:none;
        }
        .priority-2{
            display:none;
        }
        .priority-3{
            font-size:x-small;
            max-width: 100px;
        }
        .priority-4{
            display:none;
        }
        .priority-5{
            font-size:small;
        }
        .priority-6{
            font-size:small;
        }
        .priority-7{
            font-size:small;
        }
        table td {
            padding: 0px 0px !important;
        }
        .single_add_to_cart_button
        {
            display: none;
        }
        hr
        {
            display: none !important;
        }
    }
</style>
<script>
    $(document).ready(function () {
        $('#example').DataTable(
            {
                pageLength: 50,
                lengthMenu: [10, 50, 100, 200, 500],
                "bLengthChange" : false, //thought this line could hide the LengthMenu
                "bInfo":false,
                searching: false,
                order: [[1, 'desc']],
            }
        );

        $(".updateValue").on("change", function(e) {
            e.preventDefault();

            var tr = $(this).closest("tr");
            var childTr = tr.siblings("tr.child");

            var total = $(this).val() ;
alert(total);
            // tr.find(".txt_no_responsive_column").val(total);
            // tr.find(".txt_responsive_column").val(total);
            //
            // tr.find(".txt_no_responsive_column").attr("value", total);
            // tr.find(".txt_responsive_column").attr("value", total);
            //
            // childTr.find(".txt_no_responsive_column").val(total);
            // childTr.find(".txt_responsive_column").val(total);

        });
    });

    function saveQ(id)
    {
        var quantity=null;
        var price =null;
        if (typeof(document.getElementById("quantity"+id)) != 'undefined' && document.getElementById("quantity"+id) != null)
        {
            quantity=document.getElementById("quantity"+id).value
        }
        if (typeof(document.getElementById("price"+id)) != 'undefined' && document.getElementById("price"+id) != null)
        {
            price=document.getElementById("price"+id).value;
        }
        var submit=document.getElementById("submit"+id)
        var loader=document.getElementById("loader"+id);

        $.ajax({
            type: 'post',
            url: '<?php echo WP_CONTENT_URL; ?>/plugins/product-list-management/public/index.php?action=update',
            data: {
                id:id,
                quantity:quantity,
                price:price,
            },
            beforeSend: function(){
                loader.style.display="";
                submit.style.display="none";
            },
            complete: function(){
                loader.style.display="none";
                submit.style.display="";
            },
            success: async function (result) {
                if (result=='true')
                {
                    alert('آپدیت انجام شد')
                }
                else
                {
                    alert('خطا در ذخیره سازی اطلاعات!')
                }
            }

        });

    }

    function updateValue(id)
    {
        var quantity=document.getElementById("quantity"+id).value
        //alert(quantity)
    }
</script>
<form>
    <div class="row col-md-12">
        <input type="text" name="code" class="col-md-2" placeholder="کد قفسه" value="<?php echo $queryCode ; ?>">
        <input type="text" name="title" class="col-md-3" placeholder="جستجوی نام محصول" value="<?php echo $queryTitle; ?>">
        <select name="category" class="col-md-3">
            <option value="">دسته بندی انتخاب کنید</option>
            <?php
            foreach ($all_categories as $cat) {
                if ($_GET['category'] and $_GET['category']==$cat->term_id)
                {
                    $selected='selected="selected"';
                }
                else
                {
                    $selected=null;
                }
                if($cat->category_parent == 0) {
                    $category_id = $cat->term_id;
                    echo '<option '.$selected.' value="'.$cat->term_id.'">'. $cat->name .'</option>';
                }
            }
            ?>

        </select>
        <select name="brand" class="col-md-3">
            <option value="">برند انتخاب کنید</option>
            <option <?php if ($_GET['brand'] and $_GET['brand']=='1489') { echo "selected='selected'";} ?> value="1489">allnutrition</option>
            <option <?php if ($_GET['brand'] and $_GET['brand']=='1481') { echo "selected='selected'";} ?> value="1481">applied-nutrition</option>
            <option <?php if ($_GET['brand'] and $_GET['brand']=='1458') { echo "selected='selected'";} ?> value="1458">biotech</option>
            <option <?php if ($_GET['brand'] and $_GET['brand']=='1494') { echo "selected='selected'";} ?> value="1494">everbuild</option>
            <option <?php if ($_GET['brand'] and $_GET['brand']=='1488') { echo "selected='selected'";} ?> value="1488">ihs</option>
            <option <?php if ($_GET['brand'] and $_GET['brand']=='1460') { echo "selected='selected'";} ?> value="1460">ironmaxx</option>
            <option <?php if ($_GET['brand'] and $_GET['brand']=='1493') { echo "selected='selected'";} ?> value="1493">olimp</option>
            <option <?php if ($_GET['brand'] and $_GET['brand']=='1462') { echo "selected='selected'";} ?> value="1462">qnt</option>
            <option <?php if ($_GET['brand'] and $_GET['brand']=='1459') { echo "selected='selected'";} ?> value="1459">scitec</option>
            <option <?php if ($_GET['brand'] and $_GET['brand']=='1461') { echo "selected='selected'";} ?> value="1461">trec</option>
            <option <?php if ($_GET['brand'] and $_GET['brand']=='1487') { echo "selected='selected'";} ?> value="1487">yamamato</option>
        </select>
        <button type="submit" class="col-md-1">جستجو</button>
    </div>

</form>

<br>
<table id="example" class="display responsive table table-bordered" style="border: 1px solid #aaa;border-radius:3px">
    <thead class="thead-light">
    <tr>
        <th class="priority-1" style="text-align: right" scope="col">کد قفسه</th>
        <th class="priority-2" style="text-align: right" scope="col">تصویر محصول</th>
        <th class="priority-3" style="text-align: right" scope="col">نام</th>
        <th class="priority-4" style="text-align: right" scope="col">دسته</th>
        <th class="priority-5" style="text-align: right" scope="col">قیمت</th>
        <th class="priority-6" style="text-align: right" scope="col">انبار</th>
        <th class="priority-7" style="text-align: right" scope="col"></th>
    </tr>
    </thead>
    <tbody>
    <?php
    while ($loopProducts->have_posts() ) : $loopProducts->the_post();
        global $product;
        $id=$product->get_id();

        $sqlTarget = "SELECT * FROM  $product_target_list_log where productId='$id'";
        $resultsTarget = $wpdb->get_row($sqlTarget);

        $sqlThreshold = "SELECT * FROM  $product_updated_list_settings where id=1";
        $resultsThreshold = $wpdb->get_row($sqlThreshold);

        $sqlThresholdEnd = "SELECT * FROM  $product_updated_list_settings where id=2";
        $resultsThresholdEnd= $wpdb->get_row($sqlThresholdEnd);

        $sql_wip_cabinet_code = "SELECT * FROM  $wp_postmeta where post_id='$id' and meta_key='_wip_cabinet_code' ";
        $resultsSql_wip_cabinet_code= $wpdb->get_row($sql_wip_cabinet_code);

        if (isset($resultsTarget->color))
        {
            $color=$resultsTarget->color;
        }

        if (isset($resultsThreshold->color) && $product->get_stock_quantity()<=$resultsThreshold->inventory)
        {
            $color=$resultsThreshold->color;
        }

        if (isset($resultsThresholdEnd->color) && $product->get_stock_quantity()<=$resultsThresholdEnd->inventory)
        {
            $color=$resultsThresholdEnd->color;
        }



        if ($_GET['brand'] and $_GET['brand']==$product->get_attributes()['pa_brand']['options'][0])
        {
            if ($_GET['code'] and $_GET['code']!='null' and $resultsSql_wip_cabinet_code->meta_value==$_GET['code'])
            {
                ?>
                <tr bgcolor="<?php echo $color ?>" style="background-color: <?php echo $color ?>">
                    <th scope="row"><?php echo $resultsSql_wip_cabinet_code->meta_value; ?></th>
                    <td><?php echo woocommerce_get_product_thumbnail() ?></td>
                    <td><?php echo $product->get_name() ?></td>
                    <td><?php echo $product->get_categories(); ?></td>
                    <td>
                        <?php
                        if (isset($resultsPricePermission->userRoleId) and $resultsPricePermission->userRoleId==ucfirst($user->roles[0]) or $resultsPricePermission->userRoleId=='All')
                        {
                            ?>
                            <div class="row col-md-12">
                                <input type="number" id="price<?php echo $product->get_id(); ?>" name="count" class="form-controller" value="<?php echo $product->get_price(); ?>">
                            </div>
                            <?
                        }
                        else
                        {
                            echo $product->get_price_html();

                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if (isset($resultsQuantityPermission->userRoleId) and $resultsQuantityPermission->userRoleId==ucfirst($user->roles[0]) or $resultsQuantityPermission->userRoleId=='All')
                        {
                            ?>
                            <div class="row col-md-12">
                                <input onchange="updateValue(quantity<?php echo $product->get_id(); ?>)" type="number" id="quantity<?php echo $product->get_id(); ?>" name="count" class="form-controller" value="<?php echo $product->get_stock_quantity() ?>">
                            </div>
                            <?
                        }
                        else
                        {
                            if ($product->get_stock_quantity()==0)
                            {
                                echo "ناموجود";
                            }
                            else
                            {
                                echo " موجود";
                            }
                        }
                        ?>
                    </td>
                    <td>
                        <button style="width: 100%;background-color: #3498db" id="submit<?php echo $product->get_id(); ?>" onclick="saveQ(<? echo $product->get_id()?>)" type="submit" class="form-controller">ذخیره</button>
                        <div id="loader<?php echo $product->get_id(); ?>" class="loader" style="display: none">
                        </div>
                        <hr>
                        <a  href="<?php echo $product->add_to_cart_url() ?>"> <button style="width: 100%" class="single_add_to_cart_button button alt wp-element-button">افزودن به سبد خرید</button></a>
                    </td>
                </tr>
                <?
            }
            if(!$_GET['code'])
            {
                ?>
                <tr bgcolor="<?php echo $color ?>" style="background-color: <?php echo $color ?>">
                    <th scope="row"><?php echo $resultsSql_wip_cabinet_code->meta_value; ?></th>
                    <td><?php echo woocommerce_get_product_thumbnail() ?></td>
                    <td><?php echo $product->get_name() ?></td>
                    <td><?php echo $product->get_categories(); ?></td>
                    <td>
                        <?php
                        if (isset($resultsPricePermission->userRoleId) and $resultsPricePermission->userRoleId==ucfirst($user->roles[0]) or $resultsPricePermission->userRoleId=='All')
                        {
                            ?>
                            <div class="row col-md-12">
                                <input type="number" id="price<?php echo $product->get_id(); ?>" name="count" class="form-controller" value="<?php echo $product->get_price(); ?>">
                            </div>
                            <?
                        }
                        else
                        {
                            if ($product->get_stock_quantity()==0)
                            {
                                echo "ناموجود";
                            }
                            else
                            {
                                echo " موجود";
                            }
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if (isset($resultsQuantityPermission->userRoleId) and $resultsQuantityPermission->userRoleId==ucfirst($user->roles[0]) or $resultsQuantityPermission->userRoleId=='All')
                        {
                            ?>
                            <div class="row col-md-12">
                                <input type="number" id="quantity<?php echo $product->get_id(); ?>" name="count" class="form-controller" value="<?php echo $product->get_stock_quantity() ?>">
                            </div>
                            <?
                        }
                        else
                        {
                            if ($product->get_stock_quantity()==0)
                            {
                                echo "ناموجود";
                            }
                            else
                            {
                                echo "موجود";
                            }
                        }
                        ?>
                    </td>
                    <td>
                        <button style="width: 100%;background-color: #3498db" id="submit<?php echo $product->get_id(); ?>" onclick="saveQ(<? echo $product->get_id()?>)" type="submit" class="form-controller">ذخیره</button>
                        <div id="loader<?php echo $product->get_id(); ?>" class="loader" style="display: none">
                        </div>
                        <hr>
                        <a  href="<?php echo $product->add_to_cart_url() ?>"> <button style="width: 100%" class="single_add_to_cart_button button alt wp-element-button">افزودن به سبد خرید</button></a>
                    </td>
                </tr>
                <?
            }
        }
        if (!$_GET['brand'] or $_GET['brand']=='')
        {
            if ($_GET['code'] and $_GET['code']!='null' and $resultsSql_wip_cabinet_code->meta_value==$_GET['code'])
            {
                ?>
                <tr bgcolor="<?php echo $color ?>" style="background-color: <?php echo $color ?>">
                    <th scope="row"><?php echo $resultsSql_wip_cabinet_code->meta_value; ?></th>
                    <td><?php echo woocommerce_get_product_thumbnail() ?></td>
                    <td><?php echo $product->get_name() ?></td>
                    <td><?php echo $product->get_categories(); ?></td>
                    <td>
                        <?php
                        if (isset($resultsPricePermission->userRoleId) and $resultsPricePermission->userRoleId==ucfirst($user->roles[0]) or $resultsPricePermission->userRoleId=='All')
                        {
                            ?>
                            <div class="row col-md-12">
                                <input type="number" id="price<?php echo $product->get_id(); ?>" name="count" class="form-controller" value="<?php echo $product->get_price(); ?>">
                            </div>
                            <?
                        }
                        else
                        {
                            echo $product->get_price_html();

                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if (isset($resultsQuantityPermission->userRoleId) and $resultsQuantityPermission->userRoleId==ucfirst($user->roles[0]) or $resultsQuantityPermission->userRoleId=='All')
                        {
                            ?>
                            <div class="row col-md-12">
                                <input type="number" id="quantity<?php echo $product->get_id(); ?>" name="count" class="form-controller" value="<?php echo $product->get_stock_quantity() ?>">
                            </div>
                            <?
                        }
                        else
                        {
                            if ($product->get_price()===0)
                            {
                                echo "ناموجود";
                            }
                            else
                            {
                                echo " موجود";
                            }
                        }
                        ?>
                    </td>
                    <td>
                        <button style="width: 100%;background-color: #3498db" id="submit<?php echo $product->get_id(); ?>" onclick="saveQ(<? echo $product->get_id()?>)" type="submit" class="form-controller">ذخیره</button>
                        <div id="loader<?php echo $product->get_id(); ?>" class="loader" style="display: none">
                        </div>
                        <hr>
                        <a  href="<?php echo $product->add_to_cart_url() ?>"> <button style="width: 100%" class="single_add_to_cart_button button alt wp-element-button">افزودن به سبد خرید</button></a>
                    </td>
                </tr>
                <?
            }
            if(!$_GET['code'])
            {
                ?>
                <tr bgcolor="<?php echo $color ?>" style="background-color: <?php echo $color ?>">
                    <th class="priority-1"  scope="row"><?php echo $resultsSql_wip_cabinet_code->meta_value; ?></th>
                    <td class="priority-2" ><?php echo woocommerce_get_product_thumbnail() ?></td>
                    <td class="priority-3" ><?php echo $product->get_name() ?></td>
                    <td class="priority-4" ><?php echo $product->get_categories(); ?></td>
                    <td class="priority-5" >
                        <?php
                        if (isset($resultsPricePermission->userRoleId) and $resultsPricePermission->userRoleId==ucfirst($user->roles[0]) or $resultsPricePermission->userRoleId=='All')
                        {
                            ?>
                            <div class="row col-md-12">
                                <input type="number" id="price<?php echo $product->get_id(); ?>" name="count" class="form-controller" value="<?php echo $product->get_price(); ?>">
                            </div>
                            <?
                        }
                        else {
                            echo $product->get_price_html();
                        }
                        ?>
                    </td>
                    <td class="priority-6" >
                        <?php
                        if (isset($resultsQuantityPermission->userRoleId) and $resultsQuantityPermission->userRoleId==ucfirst($user->roles[0]) or $resultsQuantityPermission->userRoleId=='All')
                        {
                            ?>
                            <div class="row col-md-12">
                                <input onchange="updateValue(<?php echo $product->get_id(); ?>)" type="number" id="quantity<?php echo $product->get_id(); ?>" name="count" class="form-controller" value="<?php echo $product->get_stock_quantity() ?>">
                            </div>
                            <?
                        }
                        else
                        {
                            if ($product->get_stock_quantity()===0)
                            {
                                echo "ناموجود";
                            }
                            else
                            {
                                echo " موجود";
                            }
                        }
                        ?>
                    </td>
                    <td class="priority-7" >
                        <button style="width: 100%;background-color: #3498db" id="submit<?php echo $product->get_id(); ?>" onclick="saveQ(<? echo $product->get_id()?>)" type="submit" class="form-controller">ذخیره</button>
                        <div id="loader<?php echo $product->get_id(); ?>" class="loader" style="display: none">
                        </div>
                        <hr>
                        <a  href="<?php echo $product->add_to_cart_url() ?>"> <button style="width: 100%" class="single_add_to_cart_button button alt wp-element-button">افزودن به سبد خرید</button></a>
                    </td>
                </tr>
                <?
            }
        }
        $color=null;
    endwhile;
    ?>
    </tbody>
</table>
<?php
wp_reset_query();
?>



