<?php
/* Smarty version 3.1.29, created on 2016-04-07 02:59:04
  from "/home/dynamoelectric/public_html/dev.rock/public_html/r.frontend/template/html/_default.html" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_5705ccf87eca32_49811532',
  'file_dependency' => 
  array (
    '2f88679970b797b69d3b2712d3aacabbc2edf5f4' => 
    array (
      0 => '/home/dynamoelectric/public_html/dev.rock/public_html/r.frontend/template/html/_default.html',
      1 => 1459997868,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5705ccf87eca32_49811532 ($_smarty_tpl) {
?>
<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>

        <?php echo $_smarty_tpl->tpl_vars['METADATA']->value;?>

        <meta name="viewport" content="width=device-width, initial-scale=1">

        <?php if (isset($_smarty_tpl->tpl_vars['CSS']->value)) {?>
        <?php
$_from = $_smarty_tpl->tpl_vars['CSS']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_css_0_saved_item = isset($_smarty_tpl->tpl_vars['css']) ? $_smarty_tpl->tpl_vars['css'] : false;
$_smarty_tpl->tpl_vars['css'] = new Smarty_Variable();
$_smarty_tpl->tpl_vars['css']->_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['css']->value) {
$_smarty_tpl->tpl_vars['css']->_loop = true;
$__foreach_css_0_saved_local_item = $_smarty_tpl->tpl_vars['css'];
?>
        <link rel='stylesheet' href="<?php echo $_smarty_tpl->tpl_vars['css']->value;?>
" type="text/css"/>
        <?php
$_smarty_tpl->tpl_vars['css'] = $__foreach_css_0_saved_local_item;
}
if ($__foreach_css_0_saved_item) {
$_smarty_tpl->tpl_vars['css'] = $__foreach_css_0_saved_item;
}
?>
        <?php }?>
        <link rel="stylesheet" href="<?php echo $_smarty_tpl->tpl_vars['FRONTEND_CSS']->value;?>
bootstrap.min.css" type="text/css"/>

        <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['JQUERY']->value;?>
"><?php echo '</script'; ?>
>
        <?php if (isset($_smarty_tpl->tpl_vars['JS']->value)) {?>
        <?php
$_from = $_smarty_tpl->tpl_vars['JS']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_js_1_saved_item = isset($_smarty_tpl->tpl_vars['js']) ? $_smarty_tpl->tpl_vars['js'] : false;
$_smarty_tpl->tpl_vars['js'] = new Smarty_Variable();
$_smarty_tpl->tpl_vars['js']->_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['js']->value) {
$_smarty_tpl->tpl_vars['js']->_loop = true;
$__foreach_js_1_saved_local_item = $_smarty_tpl->tpl_vars['js'];
?>
        <?php echo '<script'; ?>
 type='text/javascript' src="<?php echo $_smarty_tpl->tpl_vars['js']->value;?>
"><?php echo '</script'; ?>
>
        <?php
$_smarty_tpl->tpl_vars['js'] = $__foreach_js_1_saved_local_item;
}
if ($__foreach_js_1_saved_item) {
$_smarty_tpl->tpl_vars['js'] = $__foreach_js_1_saved_item;
}
?>
        <?php }?>
    </head>
    <body>

        <div id='cssmenu'>
             
               <?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['MENU'][0][0]->showNavigation(array('id'=>0),$_smarty_tpl);?>

        </div>
              
        <!--PAGE CONTENT GO HERE-->
        <?php echo $_smarty_tpl->tpl_vars['PAGECONTENT']->value;?>




        <?php echo $_smarty_tpl->tpl_vars['BOOTSRAP_JS']->value;?>



        <footer></footer>
    </body>
</html>
<?php }
}
