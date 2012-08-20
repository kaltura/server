<h2>Terminology</h2>
<p>The table below provides information on some terms and definitions used in the Kaltura open source video platform, and in any relevant documentation. </p>
<table id="terminology">
  <colgroup>
          <col class="name"/>
  </colgroup>
  <tr>
    <th class="title">Term</th>
    <th class="title">Description</th>
  </tr>
  <tr class="odd">
    <td><a href="#entry">Entry</a></td>
    <td>An entry is a media entity and its meta-data on the Kaltura servers. It can be an image, a sound clip, a video file or a rough-cut (explained below) wrapped with vast information such as dates, titles, tags, ratings and more. The Kaltura extensive API enables its partners to create galleries, lists, playlists and more by calling up various entry-related API calls as well as edit this information on the fly.</td>
  </tr>
  <tr>
    <td>Rough-cut</td>
    <td>
A rough-cut is a 'mix' type entry. Similar to a video type entry, you can play a rough-cut in the Kaltura Dynamic Player (KDP) - It can be a single or a mix of videos, a slide-show, a mix of both and much more.<br />
A rough-cut is an XML unique format invented by Kaltura, similar to Final Cut Pro 'Edit Decision List' but with a twist. Whenever a user uses the Kaltura Editor to create a mix of videos and images, in and out points, transitions, text overlays, soundtrack, effects and much more - the Kaltura Editor automatically saves this mix as a rough-cut. The user can then play the rough-cut in a Kaltura video player as if it was an ordinary (yet very cool) video.</td>
  </tr>
  <tr class="odd">
    <td><a href="#widget">Widget</a></td>
    <td>A widget represents a Kaltura Dynamic Player (KDP) which is either bound to a specific media clip (in the case of media embedded in an external site) or a KDP that can show a gallery of media clips which can be selected via flashvars and javascript calls.</td>
  </tr>
  <tr>
    <td>KDP</td>
    <td>  	
The Kaltura Dynamic Player (KDP) is a highly flexible media player allowing any developer to create his own skinned and customized version which may include different sets of components. </td>
  </tr>
  <tr class="odd">
    <td>Contributor Wizard</td>
    <td>The Kaltura Contributor Wizard (aka CW or KCW) also known as the Kaltura Media Uploader, is a customizable wizard enabling end users to upload and import media. The wizard supports multiple file uploads, webcam and microphone recording and importing media from external sources (e.g. YouTube, Flickr, etc.). The wizard lets developers add their own media provider flex modules. </td>
  </tr>
  <tr>
    <td>Standard Editor</td>
    <td>The Kaltura Standard Editor (aka SE or KSE) is a customizable flash video editor enabling end users to mash up videos and images, add audio tracks and enrich their video mix with transitions. </td>
  </tr>
  <tr class="odd">
    <td><a href="#uiconf">UI Conf</a></td>
    <td>XML configuration file for various Kaltura components (KDP, KSE, KCW)</td>
  </tr>
  <tr>
    <td>Partner</td>
    <td>An organization or individual that is authorized to access the APIs, and that has a Partner ID </td>
  </tr>
  <tr class="odd">
    <td><a href="#user">User (Partner user)</a></td>
    <td>Representation of a Partner's user in Kaltura's database</td>
  </tr>
  <tr>
    <td>Metadata</td>
    <td><a href="http://corp.kaltura.com/wiki/index.php/SDL_-_Sequence_Description_Language">Read here</a></td>
  </tr>
  <tr class="odd">
    <td>CDN</td>
    <td><a href="http://en.wikipedia.org/wiki/Content_Delivery_Network">Content Delivery Network</a></td>
  </tr>
  <tr>
    <td>Notifications</td>
    <td>Sent by the Kaltura servers to a Partner system. These notifications enable a partner system to provide its own searching capabilities and caching for Kaltura stored data. The Kaltura servers log required operations and then send notifications asynchronously.</td>
  </tr>
</table>