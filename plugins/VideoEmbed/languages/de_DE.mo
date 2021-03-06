��          �      l      �  �  �  �  �  ]  $  +   �     �     �  ^   �     2     @     O     _  )   q  X   �     �  J   	  7   R	     �	     �	  ]   �	  _   
  �  }
  �  v  �    t  �  9        N     c  q   ~     �     �          &  ;   8  _   t     �  ]   �  J   G     �  %   �  l   �  x   C                                              
                                          	          
          If you upload a video file to Omeka (or any other kind of file for that matter), it will be part of that particular
          item that you added it to. Sometimes, however, it might be desirable to reference a video file from different other
          items. A good example for this is when you upload a video file that contains references to multiple icons; files
          can not be attached to multiple items at once.
         
          To reference a video file, first find out its numerical ID. The easiest way is to check the content that it is part of
          and click on its video placeholder icon. <em>Please note:</em> The files use a separate numbering than the items:
          Item ID and file ID are not the same thing. – For example, let us say that you found the video ID to be #42.
         
          With this plugin, you may refence videos by adding a “pseudo code tag” anywhere in your item's content. It doesn't
          matter where you add it, it could be in some comment field or anywhere else. This way, the video will be displayed
          below your regular item's content. Even better: You may specify a specific timecode, so the video reference will
          give you the ability to play only one particular segment from that video. This frees you from the need to
          upload videos multiple times and/or to split it into multiple pieces and upload those.
         Click here to play "%1$s" from %2$s to %3$s Complete Video Display Related items Display related items that also embed the same video, sorted after their respective timecodes. Documentation Embedded Video Embedded Videos From %1$s to %2$s Related items also referencing this video Remove "{{#xx}}" / "{{#xx;yy-zz}}" pseudo code tags when displaying content (see below). Remove Pseudo Code To play video #42 from 0:50 to 1:10, enter: <strong>{{#42;50-70}}</strong> To reference video #42, enter: <strong>{{#42}}</strong> Video Width in Admin View Video Width in Public View Width of the embedded video viewer in admin backend, in pixels. – Enter "0" for 100% width. Width of the embedded video viewer in public frontend, in pixels. – Enter "0" for 100% width. Project-Id-Version: VideoEmbed
Report-Msgid-Bugs-To: https://github.com/GerZah/wesa_omeka/issues
POT-Creation-Date: 2012-01-09 21:49-0500
PO-Revision-Date: 2016-08-10 17:58+0200
Last-Translator: Gero Zahn <gerozahn@campus.uni-paderborn.de>, 2016
Language-Team: German (Germany) (https://www.transifex.com/upb/teams/56327/de_DE/)
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
Language: de_DE
Plural-Forms: nplurals=2; plural=(n != 1);
X-Generator: Poedit 1.8.8
 Wenn Sie eine Video-Datei zu Omeka hochladen (bzw. in dieser Hinsicht jede andere Art von Datei), so wird Sie Teil eines bestimmten Objekts sein. Manchmal kann es jedoch wünschenswert sein, ein Video aus anderen Objekten zu referenzieren. Ein gutes Beispiel hierfür ist, wenn Sie ein Video hochladen, das Referenzen auf mehrere Objekte enthält; Dateien können nicht mehreren Objekten gleichzeitig zugeordnet sein. Um ein Video zu referenzieren, ermitteln Sie zuerst dessen numerische ID. Am einfachsten ist es, den Inhalt in dem es enthalten ist anzusehen und auf das Video-Platzhalter-Symbol zu klicken. <em>Bitte beachten Sie:</em> Dateien benutzen eine andere Nummerierung als Objekte: Objekt-ID und Datei-ID sind nicht dasselbe. – Nehmen wir zum Beispiel an, Sie hätten die Video-ID #42 ermittelt. Mit diesem Plugin können Sie Videos durch Hinzufügen eines „Pseudocode-Tags“ irgendwo in Ihrem Objekt-Inhalt referenzieren. Es kommt nicht darauf an, wo Sie dieses hinzufügen: Es könnte in irgendeinem Kommentarfeld oder anderswo sein. Auf fiese Weise wird das Video unterhalb des normalen Objekt-Inhaltes dargestellt. Noch besser: Sie können einen bestimmten Timecode spezifizieren, wodurch Sie die Möglichkeit haben, nur ein bestimmtes Segment des referenzierten Videos abzuspielen. Dadurch entfällt für Sie der Bedarf, das Video mehrfach hochzuladen oder es gar in mehrere Teile aufzuspalten und diese hochzuladen. Klicken Sie hier, um "%1$s" von %2$s bis %3$s abzuspielen Vollständiges Video Verwandte Objekte anzeigen Verwandte Objekte anzeigen, die ebenfalls dasselbe Video referenzieren, sortiert nach deren jeweiligen Timecodes. Dokumentation Eingebettetes Video Eingebettete Videos Von %1$s bis %2$s Verwandte Objekte, die dieses Video ebenfalls referenzieren "{{#xx}}" / "{{#xx;yy-zz}}" Pseudocode-Tags bei der Anzeige von Inhalt entfernen (siehe unten). Pseudocode entfernen Um das Video #42 von 0:50 bis 1:10 abzuspielen, schreiben Sie: <strong>{{#42;50-70}}</strong> Um das Video #42 zu referenzieren, schreiben Sie: <strong>{{#42}}</strong> Video-Breite in Admin-Ansicht Video-Breite in Öffentlicher Ansicht Breite in Pixeln der eingebetteten Video-Ansicht im Admin-Backend. – Geben Sie "0" für "100%" Breite ein. Breite in Pixeln der eingebetteten Video-Ansicht in der Öffentlichen Ansicht. – Geben Sie "0" für "100%" Breite ein. 