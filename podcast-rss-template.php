<?php
/**
 * With thanks to @geoffreygraham for the ideas at:
 * @link https://css-tricks.com/roll-simple-wordpress-podcast-plugin/
 * @version Reworked by @tristanmason
 */

// Query the wpv_sermon Custom Post Type and fetch the latest 50 posts
    $args = array( 'post_type' => 'wpv_sermon', 'posts_per_page' => 50 );
    $loop = new WP_Query( $args );

/**
 * Get the current URL taking into account HTTPS and Port
 * @link http://css-tricks.com/snippets/php/get-current-page-url/
 * @version Refactored by @AlexParraSilva
 */
function getCurrentUrl() {
    $url  = isset( $_SERVER['HTTPS'] ) && 'on' === $_SERVER['HTTPS'] ? 'https' : 'http';
    $url .= '://' . $_SERVER['SERVER_NAME'];
    $url .= in_array( $_SERVER['SERVER_PORT'], array('80', '443') ) ? '' : ':' . $_SERVER['SERVER_PORT'];
    $url .= $_SERVER['REQUEST_URI'];
    return $url;
}
  
// Output the XML header
header('Content-Type: '.feed_content_type('rss-http').'; charset='.get_option('blog_charset'), true);
echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>';


// Define the namespaces
?>
<rss version="2.0"
  xmlns:content="http://purl.org/rss/1.0/modules/content/"
  xmlns:dc="http://purl.org/dc/elements/1.1/"
  xmlns:atom="http://www.w3.org/2005/Atom"
  xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
  xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
  xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd"
  xmlns:rawvoice="http://www.rawvoice.com/rawvoiceRssModule/"
  xmlns:googleplay="http://www.google.com/schemas/play-podcasts/1.0/play-podcasts.xsd"
  <?php do_action('rss2_ns'); ?>>

    <channel>
        <title>Christ Community Church of Plainfield Sermons</title>
        <link><? bloginfo_rss('url') ?></link>
  <atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
        <language><? bloginfo_rss('language'); ?></language>
  <sy:updatePeriod><?php echo apply_filters( 'rss_update_period', 'hourly' ); ?></sy:updatePeriod>
        <sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', '1' ); ?></sy:updateFrequency>
  <lastBuildDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false); ?></lastBuildDate>
        <description><? bloginfo_rss('description') ?> &#x2295; Christ Community Church is located in the Chicago suburb of Plainfield. We are a people from different backgrounds, with different interests, with varying viewpoints on just about any subject, but one thing unites us—we have found forgiveness, grace, and life in Jesus Christ. We don’t have it all figured out, we struggle in life and with faith, but we are really glad we can do that as a part of this loving community of grace. We are reminding one another that the gospel is true and Jesus is everything. You&#039;re invited to join us in our journey, whether in person or through our podcast! For more info visit <? bloginfo_rss('url') ?></description>
        <itunes:summary><? bloginfo_rss('description') ?> &#x2295; Christ Community Church is located in the Chicago suburb of Plainfield. We are a people from different backgrounds, with different interests, with varying viewpoints on just about any subject, but one thing unites us—we have found forgiveness, grace, and life in Jesus Christ. We don’t have it all figured out, we struggle in life and with faith, but we are really glad we can do that as a part of this loving community of grace. We are reminding one another that the gospel is true and Jesus is everything. You&#039;re invited to join us in our journey, whether in person or through our podcast! For more info visit <? bloginfo_rss('url') ?></itunes:summary>
<itunes:author>Christ Community Church of Plainfield</itunes:author>
<itunes:explicit>no</itunes:explicit>
<itunes:image href="<? echo site_url(); ?>/wp-content/uploads/2017/06/ccc-podcast-art-3000.jpg" />
<itunes:owner>
<itunes:name>Christ Community Church of Plainfield Sermons</itunes:name>
<itunes:email>sermons@cccplainfield.org</itunes:email>
</itunes:owner>
<itunes:subtitle>Preaching centered upon the work of Jesus and connected to our everyday lives</itunes:subtitle>
<image>
<title>Christ Community Church of Plainfield Sermons</title>
<url><?php echo site_url(); ?>/wp-content/uploads/2017/06/ccc-podcast-art-3000.jpg</url>
<link>https://cccplainfield.org</link>
</image>
<itunes:category text="Religion &amp; Spirituality">
<itunes:category text="Christianity" />
</itunes:category>
        <itunes:keywords>christ community church of plainfield,christ community,church,ryan stanley,plainfield,sermon,message,teaching,bible,pastor</itunes:keywords>
  <rawvoice:rating>TV-G</rawvoice:rating>
  <rawvoice:location>Plainfield, IL</rawvoice:location>
  <rawvoice:frequency>Weekly</rawvoice:frequency>
  <dc:creator>Christ Community Church of Plainfield</dc:creator>
         <category><![CDATA[Podcast]]></category>
  <category><![CDATA[Christianity]]></category>
  <googleplay:author>Christ Community Church of Plainfield</googleplay:author>
  <googleplay:email>sermons@cccplainfield.org</googleplay:email>
  <googleplay:description><? bloginfo_rss('description') ?> &#x2295; Christ Community Church is located in the Chicago suburb of Plainfield. We are a people from different backgrounds, with different interests, with varying viewpoints on just about any subject, but one thing unites us—we have found forgiveness, grace, and life in Jesus Christ. We don’t have it all figured out, we struggle in life and with faith, but we are really glad we can do that as a part of this loving community of grace. We are reminding one another that the gospel is true and Jesus is everything. You&#039;re invited to join us in our journey, whether in person or through our podcast! For more info visit <? bloginfo_rss('url') ?></googleplay:description>
  <googleplay:category text="Religion &amp; Spirituality" />

<?php // Start the loop for Podcast posts

    while ( $loop->have_posts() ) : $loop->the_post(); ?>
    <item>
      <title><?php the_title_rss(); ?></title>
      <itunes:author><?php echo get_the_author() . ' at Christ Community'; ?></itunes:author>
  	<dc:creator><?php the_author(); ?></dc:creator>
	<itunes:explicit>no</itunes:explicit>
      <description><?php echo get_the_excerpt() . ' '; ?>Read more or watch video at <?php echo get_permalink(); ?></description>
      <itunes:summary><![CDATA[<?php echo get_the_excerpt() . ' '; ?>Read more or watch video at <?php echo get_permalink(); ?>]]></itunes:summary>
      <?php // Retrieve just the URL of the Featured Image: http://codex.wordpress.org/Function_Reference/wp_get_attachment_image_src
      if (has_post_thumbnail( $post->ID ) ): ?>
        <?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' ); ?>
        <itunes:image href="<?php echo $image[0]; ?>" />
      <?php endif; ?>
      
      <?php // Get the mp3 URL, filesize and date format
        $audiolink = get_post_meta( get_the_ID(), 'wpv-sermon-audio', 'single' );
      $videolink = get_post_meta( get_the_ID(), 'wpv-sermon-video', 'single' );
      $doculink = get_post_meta( get_the_ID(), 'wpv-sermon-document', 'single' );
        $filesize = get_post_meta( get_the_ID(), '_mp3_file_size', 'single' );
      $duration = get_post_meta( get_the_ID(), '_mp3_duration', 'single' );
      ?>
      
      <enclosure url="<?php echo $audiolink; ?>" length="<?php echo $filesize; ?>" type="audio/mpeg" />
      <guid><?php echo $audiolink; ?></guid>
  <itunes:duration><?php echo $duration; ?></itunes:duration>
      <pubDate><?= mysql2date( 'D, d M Y H:i:s +0000',
                                get_post_time( 'Y-m-d H:i:s', true), false); ?></pubDate>

    </item>
    <?php endwhile; ?>
  
  </channel>

</rss>