<?php 
/*
Plugin Name: Parole chiave in evidenza
Description: Fa essatamente quello che dice, mette le parole chiave dei tuoi post o delle tue pagine in evidenza.
Author: Ovidiu Purdea
Version: 1.0.0
Author URI: http://www.avanguardia.ro
*/

add_action('admin_menu','text_setting_menu');
add_action('admin_init','highlighted_text_register_settings');


function pce_word_highligher_install()
{
	
	 $pluginOptions = get_option('highlightedtext_options');

    if ( false === $pluginOptions ) {
        // Install plugin
		
		$highlightedtext_options['highlightedtext_bgcolor']='#ffff00';
		$highlightedtext_options['highlightedtext_fgcolor']='#000000';
		$highlightedtext_options['highlightedtext_active']='1';
		$highlightedtext_options['highlightedtext_type']='both';
		$highlightedtext_options['highlightedtext_case']='1';
		
		$default_css='.wh_highlighted
					  {
					   {background_color}
					   {foreground_color}
					   {font-style}
					   {font-weight}
					   {text-decoration}
					  }';


		$highlightedtext_options['highlightedtext_css']=$default_css;

		add_option('highlightedtext_options',$highlightedtext_options);



    } 

}

function pce_word_highligher_uninstall()
{
	delete_option('highlightedtext_options');
}

register_activation_hook(__FILE__, 'pce_word_highligher_install');

register_deactivation_hook(__FILE__, 'pce_word_highligher_uninstall');


function text_setting_menu(){
	add_options_page('Impostazioni Parole Chiave', 'Parole chiave in evidenza', 'manage_options', 'manage_settings', 'get_text_highlighted_settings');

}
function highlighted_text_register_settings(){
  register_setting( 'highlightedtext-options', 'highlightedtext_options' );
}

function get_text_highlighted_settings(){
?>
	<div class="wrap">
			<h2>Impostazioni</h2>
    		 

             <form method="post" action="options.php"> 
				<?php settings_fields('highlightedtext-options'); ?>
				<?php $options = get_option('highlightedtext_options'); ?>
            <table class="form-table">
                <tr valign="top"><th scope="row">Parole chiave in evidenza:</th>
                  <td><textarea rows="4" cols="80" name="highlightedtext_options[highlightedtext_name]"><?php  echo trim(stripslashes($options['highlightedtext_name'])); ?> </textarea><br /><span class="description">Inserisci separati da virgola. Es: seo, seo word, words seo.</span></td>
                </tr>
                <?php
				if($options['highlightedtext_bold']){
				$boldcheck="checked='checked'";
				}
				else
				{
					$boldcheck="";
				}
				
				if($options['highlightedtext_italic']){
				$italiccheck="checked='checked'";
				}
				else
				{
					$italiccheck="";
				}
				
				
				if($options['highlightedtext_underline']){
				$underlinecheck="checked='checked'";
				}
				else
				{
					$underlinecheck="";
				}
				
				?>
				<tr valign="top"><th scope="row">Applica</th>
                <td><input name="highlightedtext_options[highlightedtext_bold]" value="1" type="checkbox" <?php echo $boldcheck;?>  /> Grassetto 
                <input name="highlightedtext_options[highlightedtext_italic]" value="1" type="checkbox" <?php echo $italiccheck;?>  /> Italico 
                <input name="highlightedtext_options[highlightedtext_underline]" value="1" type="checkbox" <?php echo $underlinecheck;?>  /> Sottolineare
                
                </td>
                </tr>
                
                <tr valign="top"><th scope="row">Colore di sfondo</th>
                  <td><input class="regular-text" name="highlightedtext_options[highlightedtext_bgcolor]" type="text" value="<?php echo $options['highlightedtext_bgcolor']; ?>" /></td>
                </tr>
				<tr valign="top"><th scope="row">Colore di primo piano</th>
                  <td><input class="regular-text" name="highlightedtext_options[highlightedtext_fgcolor]" type="text" value="<?php echo $options['highlightedtext_fgcolor']; ?>" /></td>
                </tr>
				<?php
				if($options['highlightedtext_active']){
				$check="checked='checked'";
				}
				else
				{
					$check="";
				}
				?>
				<tr valign="top"><th scope="row">Attiva</th>
                <td><input name="highlightedtext_options[highlightedtext_active]" value="1" type="checkbox" <?php echo $check;?>  /><span class="description"> verifica se si deve applicare alle pagine o ai posts</span></td>
                </tr>
            
            	<?php
				if($options['highlightedtext_case']){
				$check="checked='checked'";
				}
				else
				{
					$check="";
				}
				?>
				<tr valign="top"><th scope="row">Case Sensetive</th>
                <td><input name="highlightedtext_options[highlightedtext_case]" value="1" type="checkbox" <?php echo $check;?>  /><span class="description"> verifica se e case sensetive. (raccomandato)</span></td>
                </tr>
             
				<tr valign="top"><th scope="row">Applica a </th>
                <td>
                <select name="highlightedtext_options[highlightedtext_type]" id="highlightedtext_options[highlightedtext_type]">
                <option value="post" <?php if($options['highlightedtext_type']=='post') echo "selected='selected'" ?>>Articole</option>
                <option value="page" <?php if($options['highlightedtext_type']=='page') echo "selected='selected'" ?>>Pagine</option>
                <option value="both" <?php if($options['highlightedtext_type']=='both') echo "selected='selected'" ?>>Entrambe</option>
                </select>
                </td>
                </tr>
                 <tr valign="top"><th scope="row">Classe CSS Customizato</th>
                  <td><textarea rows="8" cols="80" name="highlightedtext_options[highlightedtext_css]"><?php  echo trim(stripslashes($options['highlightedtext_css'])); ?></textarea><br /><span class="description">Classe css personalizzato applicata su parole chiave evidenziata.</span></td>
                </tr>
			</table>
                <p class="submit">
					<input type="submit" class="button-primary" value="<?php _e('Salva cambiamenti') ?>" />
				</p>
		</form>
	</div>
<?php
}


add_filter( 'the_content', 'apply_pce_word_highligher' );

function apply_pce_word_highligher( $content ) {
    
	 global $post;
		
	$post_type=get_post_type($post->ID);

	$options = get_option('highlightedtext_options');
	
	if($options['highlightedtext_type']!=$post_type and $options['highlightedtext_type']!='both')
	return $content;
	//echo "<pre>";print_r($options);
	if($options['highlightedtext_active'])
	{
	
	//echo "here=".$text."<br />";
	$text_name=explode(',',trim($options['highlightedtext_name']));
	//echo "<pre>";print_r($text_name);
	if(!empty($text_name)){
	for($i=0;$i<count($text_name);$i++){
	if(trim($text_name[$i])!=''){
	
		if(preg_match('~\b' . preg_quote($text_name[$i], '~') . '\b(?![^<]*?>)~',$content,$result))
		{
			$rep_html='<label class="wh_highlighted">'.$text_name[$i].'</label>';
		 	if($options['highlightedtext_case'])
			{
		
				$content = preg_replace('~\b' . preg_quote($text_name[$i], '~') . '\b(?![^<]*?>)~',$rep_html,$content);
				
			}
			else
			{
					$content = preg_replace('~\b' . preg_quote($text_name[$i], '~') . '\b(?![^<]*?>)~i',$rep_html,$content);
			
			}
		}
		
	}
	}
	}
	}

   
    return $content;
}

function pce_word_highligher_css()
{
	
$options = get_option('highlightedtext_options');

$css=$options['highlightedtext_css'];
//{background_color}

if($options['highlightedtext_bgcolor']!='')
$css=str_replace('{background_color}',"background-color :".$options['highlightedtext_bgcolor'].";",$css);
else
$css=str_replace('{background_color}',"",$css);

if($options['highlightedtext_fgcolor']!='')
$css=str_replace('{foreground_color}',"color :".$options['highlightedtext_fgcolor'].";",$css);
else
$css=str_replace('{foreground_color}',"",$css);

if($options['highlightedtext_bold']!='')
$css=str_replace('{font-weight}',"font-weight:bold;",$css);
else
$css=str_replace('{font-weight}',"",$css);

if($options['highlightedtext_italic']!='')
$css=str_replace('{font-style}',"font-style : italic;",$css);
else
$css=str_replace('{font-style}',"",$css);

if($options['highlightedtext_underline']!='')
$css=str_replace('{text-decoration}',"text-decoration:underline;",$css);
else
$css=str_replace('{text-decoration}',"",$css);

?>
<style>
<?php
echo $css;
?>

</style>
<?php

}

add_action('wp_head','pce_word_highligher_css');

?>