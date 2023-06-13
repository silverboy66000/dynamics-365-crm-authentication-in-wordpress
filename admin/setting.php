<?php
global $wpdb;
global $wp_roles;

$product_updated_list_settings = $wpdb->prefix . 'product_updated_list_settings';
$product_permission_list_settings = $wpdb->prefix . 'product_permission_list_settings';
$product_target_list_log  = $wpdb->prefix . 'product_target_list_log';
$active1='';
$active2='';
$active3='';
$roles = $wp_roles->get_names();
$user = wp_get_current_user();
$userid=$user->ID;


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

?>

<div class="wrap">
    <div id="icon-users" class="icon32"></div>
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
</div>
<?php
if (isset($_POST['quantityCount']))
{
    $sql = "SELECT * FROM  $product_updated_list_settings where id=1";
    $results = $wpdb->get_results($sql);
    if ($results==null)
    {
        $wpdb->insert($product_updated_list_settings, array(
                'inventory' => $_POST['quantityCount'],
                'color' => $_POST['quantityColor'],
            )
        );
    }
    else
    {
        $wpdb->update($product_updated_list_settings,
            array(
                'inventory' => $_POST['quantityCount'],
                'color' => $_POST['quantityColor'],
            ),
            array(
                'id'=>1
            )
        );
    }
    ?>
    <div class="postbox">
        <div class="inside">
            تغییرات آستانه کاهش موجودی با موفقیت ذخیره گردید!
        </div>
    </div>
    <?php
}
if (isset($_POST['quantityCountLess']))
{
    $sql = "SELECT * FROM  $product_updated_list_settings where id=2";
    $results = $wpdb->get_results($sql);
    if ($results==null)
    {
        $wpdb->insert($product_updated_list_settings, array(
                'inventory' => $_POST['quantityCountLess'],
                'color' => $_POST['quantityLessColor'],
            )
        );
    }
    else
    {
        $wpdb->update($product_updated_list_settings,
            array(
                'inventory' => $_POST['quantityCountLess'],
                'color' => $_POST['quantityLessColor'],
            ),
            array(
                'id'=>2
            )
        );
    }
    ?>
    <div class="postbox">
        <div class="inside">
            تغییرات اتمام موجودی با موفقیت ذخیره گردید!
        </div>
    </div>
    <?php
}
if (isset($_POST['userPricePermission']))
{
    $sql = "SELECT * FROM  $product_permission_list_settings where id=1";
    $results = $wpdb->get_results($sql);
    if ($results==null)
    {
        $wpdb->insert($product_permission_list_settings, array(
                'userRoleId' => $_POST['userPricePermission'],
                'canPriceUpdate' => 'price',
            )
        );
    }
    else
    {
        $wpdb->update($product_permission_list_settings,
            array(
                'userRoleId' => $_POST['userPricePermission'],
                'canPriceUpdate' => 'price',
            ),
            array(
                'id'=>1
            )
        );
    }
    ?>
    <div class="postbox">
        <div class="inside">
            تغییرات دسترسی قیمت با موفقیت ذخیره گردید!
        </div>
    </div>
    <?php
}
if (isset($_POST['userQuantityPermission']))
{
    $sql = "SELECT * FROM  $product_permission_list_settings where id=2";
    $results = $wpdb->get_results($sql);
    if ($results==null)
    {
        $wpdb->insert($product_permission_list_settings, array(
                'userRoleId' => $_POST['userQuantityPermission'],
                'canPriceUpdate' => 'quantity',
            )
        );
    }
    else
    {
        $wpdb->update($product_permission_list_settings,
            array(
                'userRoleId' => $_POST['userQuantityPermission'],
                'canPriceUpdate' => 'quantity',
            ),
            array(
                'id'=>2
            )
        );
    }
    ?>
    <div class="postbox">
        <div class="inside">
            تغییرات دسترسی تعداد با موفقیت ذخیره گردید!
        </div>
    </div>
    <?php
}
if(isset($_POST['ProductListUpdater']))
{

// delete row ##
    global $wpdb;
    $delete = $wpdb->query("TRUNCATE TABLE `$product_target_list_log`");

    foreach ($_POST['productID'] as $productIDs)
    {
        $wpdb->insert($product_target_list_log, array(
                'productId' => $productIDs,
                'userId' => $userid,
                'color' => $_POST['productIdColor'],
            )
        );
    }
    ?>
    <div class="postbox">
        <div class="inside">
            محصولات هدف با موفقیت ذخیره گردید!
        </div>
    </div>
<?php
}

$sqlThreshold = "SELECT * FROM  $product_updated_list_settings where id=1";
$resultsThreshold = $wpdb->get_row($sqlThreshold);

$sqlThresholdEnd = "SELECT * FROM  $product_updated_list_settings where id=2";
$resultsThresholdEnd= $wpdb->get_row($sqlThresholdEnd);

$sqlPermissionPrice = "SELECT * FROM  $product_permission_list_settings where canPriceUpdate='price'";
$resultSqlPermissionPrice= $wpdb->get_row($sqlPermissionPrice);

$sqlPermissionQuantity = "SELECT * FROM  $product_permission_list_settings where canPriceUpdate='quantity'";
$resultSqlPermissionQuantity= $wpdb->get_row($sqlPermissionQuantity);

$sqlTargets = "SELECT * FROM  $product_target_list_log";
$resultSqlTargets= $wpdb->get_results($sqlTargets);

?>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>
    $(document).ready(function () {
        $('#example').DataTable();
    });
</script>
<div class="wrap fsww">
    <h2 class="nav-tab-wrapper">
        <?php
        if (!isset($_GET['tab']) or isset($_GET['tab']) and $_GET['tab']=='listPermission')
        {
            $active1='nav-tab-active';
        }
        if (isset($_GET['tab']) and $_GET['tab']=='userPermission')
        {
            $active2='nav-tab-active';
        }
        if (isset($_GET['tab']) and $_GET['tab']=='productTarget')
        {
            $active3='nav-tab-active';
        }
        ?>
        <a href="admin.php?page=product_list_logs&amp;tab=listPermission" class="nav-tab <?php echo $active1; ?>">تنظیمات لیست</a>
        <a href="admin.php?page=product_list_logs&amp;tab=userPermission" class="nav-tab  <?php echo $active2; ?>">تنظیمات دسترسی</a>
        <a href="admin.php?page=product_list_logs&amp;tab=productTarget" class="nav-tab  <?php echo $active3; ?>">محصولات هدف</a>
    </h2>

    <?php
    if (!isset($_GET['tab']) or isset($_GET['tab']) and $_GET['tab']=='listPermission')
    {
        ?>
        <div class="postbox">
            <div class="inside">
                <form action="#" method="post">
                    <div class="input-box">
                        <div class="label">
                            <span>آستانه کاهش موجودی</span>
                        </div>
                        <div class="input">
                            <input class="input-field" name="quantityCount" type="text" value="<? echo $resultsThreshold->inventory ?>">
                        </div>
                    </div>
                    <div class="input-box">
                        <div class="label">
                            <span>رنگ کاهش موجودی</span>
                        </div>
                        <div class="input">
                            <input class="input-field" name="quantityColor" type="color" value="<? echo $resultsThreshold->color ?>">
                        </div>
                    </div>
<hr>
                    <div class="input-box">
                        <div class="label">
                            <span>آستانه اتمام موجودی</span>
                        </div>
                        <div class="input">
                            <input class="input-field" name="quantityCountLess" type="text" value="<? echo $resultsThresholdEnd->inventory ?>">
                        </div>
                    </div>
                    <div class="input-box">
                        <div class="label">
                            <span>رنگ کاهش موجودی</span>
                        </div>
                        <div class="input">
                            <input class="input-field" name="quantityLessColor" type="color" value="<? echo $resultsThresholdEnd->color ?>">
                        </div>
                    </div>
                    <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="ذخیرهٔ تغییرات"></p>
                </form>
            </div>
        </div>
        <?php
    }
    ?>

    <?php
    if (isset($_GET['tab']) and $_GET['tab']=='userPermission')
    {
        ?>
        <div class="postbox">
            <div class="inside">
                <form action="#" method="post">
                    <input class="input-field" name="type" type="hidden" value="">
                    <div class="input-box">
                        <div class="label">
                            <span>چه کاربرانی می توانند قیمت را ویرایش کنند؟</span>
                        </div>
                        <div class="input">
                            <select class="input-field" id="userPricePermission" name="userPricePermission">
                                <option value="All">All User</option>
                                <?php
                                foreach($roles as $role) {
                                    if ($resultSqlPermissionPrice->userRoleId==$role)
                                    {
                                        $selected='selected="selected"';
                                    }
                                    else
                                    {
                                        $selected='';
                                    }
                                    echo '<option '.$selected.' value="'.$role.'">'.$role.'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <hr>
                    <div class="input-box">
                        <div class="label">
                            <span>چه کاربرانی می توانند موجودی را ویرایش کنند؟</span>
                        </div>
                        <div class="input">
                            <select class="input-field" id="userQuantityPermission" name="userQuantityPermission">
                                <option value="All">All User</option>
                                <?php
                                foreach($roles as $role) {
                                    if ($resultSqlPermissionQuantity->userRoleId==$role)
                                    {
                                        $selected2='selected="selected"';
                                    }
                                    else
                                    {
                                        $selected2='';
                                    }
                                    echo '<option '.$selected2.' value="'.$role.'">'.$role.'</option>';

                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="ذخیرهٔ تغییرات"></p>
                </form>
            </div>
        </div>
        <?php
    }
    ?>
    <?php
    if (isset($_GET['tab']) and $_GET['tab']=='productTarget')
    {?>
        <div class="postbox">
            <div class="inside">
                <form action="#" method="post">
                    <input class="input-field" name="ProductListUpdater" type="hidden" value="1">
                    <div class="input-box">
                        <div class="label">
                            <b>محصولاتی که در تارگت فروش هستن مشخص کنید</b>
                        </div>
                    </div>
                    <div class="input-box">
                        <div class="label">
                            <label> انتخاب رنگ هدف</label>
                            <input type="color" name="productIdColor" value="<?php echo $resultSqlTargets[0]->color; ?>">
                        </div>
                    </div>
                    <hr>
                    <div class="input-box">
                        <div class="label">
                            <label> انتخاب محصول هدف</label>
                        </div>
                    </div>
                    <table class="table elementor-element elementor-element-9d736aa elementor-align-justify elementor-widget elementor-widget-button wrap">
                        <thead class="thead-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">کد قفسه</th>
                            <th scope="col">نامه</th>
                            <th scope="col">دسته</th>
                            <th scope="col">قیمت</th>
                            <th scope="col">انبار</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        while ( $loopProducts->have_posts() )
                            {
                                $loopProducts->the_post();
                            global $product;
                                $id=$product->get_id();

                                $sqlTargets = "SELECT * FROM  $product_target_list_log WHERE productId='$id'";
                                $resultSqlTargets= $wpdb->get_row($sqlTargets);
                                ?>
                        <tr>
                            <th>
                                <?php
                                if ($resultSqlTargets->productId==$id)
                                {
                                    $checked='checked="checked"';
                                }
                                else
                                {
                                    $checked='';
                                }
                                ?>
                                <input <?php echo $checked; ?> type="checkbox" name="productID[]" value="<?php echo $product->get_id() ?>">
                            </th>
                            <th scope="row"><?php $data=(array)$product->get_meta_data()[6];foreach ($data as $key=>$dataInfo) {} echo$dataInfo['value'];echo "<br>"; ?></th>
                            <td><?php echo $product->get_name() ?></td>
                            <td><?php echo $product->get_categories(); ?></td>
                            <td><?php echo $product->get_price(); ?></td>
                            <td><?php echo $product->get_stock_quantity() ?></td>

                        </tr>
                        <?php
                            }
                        ?>
                        </tbody>
                    </table>
                    <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="ذخیرهٔ تغییرات"></p>
                </form>
            </div>
        </div>
        <?php
    }
    ?>
</div>

