��            )   �      �  �  �  !  J
  �   l     '     -  i   >     �     �     �     �     �  6   �        K   7  '   �     �  �   �     �     �     �     �  !   �  �     '   �     �  ~  �     j     p     |  �  }  _  l  �   �     �     �  f   �     4  	   D     N     \     d  H   �     �  X   �  2   C      v     �      �!     �!     �!     �!  2   �!  �   ."  /   	#     9#  �  J#     %     #%                                                    	                     
                                                                 
<p>
To specify a triple unit, please use the form “a-b-c”, e.g. like this:
<pre>
yd-ft-in
m-cm-mm
</pre>
</p>
<p>
You may also specify hierarchical conversion rates between the three single units;
you can do so by adding them in round bracktets after the triple unit, e.g. like this:
<pre>
yd-ft-in (1-3-12)
m-cm-mm (1-100-10)
</pre>
By this you would have specified that (a) 1 yard equals 3 feet,
while 1 foot equals 12 inch and (b) that 1 meter equals 100 centimeters,
while 1 centimeter equals 10 millimeters. — Obviously, the first number inside
the round brackets will always be “1”.
</p>
<p>
Additionally, you may group multiple triple units into categories; you can do so
by adding the category name in box brackets before the triple unit, e.g. like this:
<pre>
[Imperial] mi-yd-ft (1-1760-3)
[Imperial] yd-ft-in (1-3-12)
[Metric] km-m-cm (1-1000-100)
[Metric] m-cm-mm (1-100-10)
</pre>
<em>Please note:</em> Assigning a group name does not require adding conversion rates.</p>
<p>
“Yard” / “feet” and “meter” / “centimeter” are specified twice in two different
triple units. Within the same group of triple units, Range Search will
automatically create a semantic coherence between identical single units, so you
will be able to convert between them, based on their respective conversion rates.
</p>
<hr>
<p>
Entering numbers or ranges (hence the name, Range Search) into metadata fields,
you may use the pop-up tool that you can reach from within the item editor. You
may also type them manually in the form given below, i.e. using the concrete
numbers together with the unit names, e.g. like this:
<pre>
1yd-2ft-3in
1m-50cm - 2m
</pre>
As you can see, you may omit the last one or two numbers and units.<br>
<em>Please note:</em> The first number (i.e. the highest significant unit) may
be up to ten digits long, while the second and third number (i.e. the two lower
significant units) may each be up to four digits long.
</p>
             <em>Explanation:</em> Range Search relies on a search index that is being created during content maintenance in the background. However, existing content will not be re-indexed automatically. So if you have existing content or modify your settings, you should re-generate the search index. <strong>Please note:</strong> Checking this box will re-generate the index <em>now</em> and exactly <em>once</em>. This action will be carried out as soon as you click on "Save Changes". Apply Auto Conversions Check this if you want numbers / ranges processing to be carried out within all of an item's text fields. Conversion Rates Convert Debug Output Entry Limit Scan to Fields Please click here to show/hide additional information. Please enter a number. Please enter all triple units that you would like to support, one per line. Please select a target text area first. Please select a unit. Please select the elements i.e. fields that the scan for names / ranges should be limited to.<br><em>Please note:</em> To select multiple entries, try holding the Ctrl key (Windows) or the Cmd key (Mac) while clicking. Range Entry Range Search Range Search Debug Output Scan All Text Fields Scan Inside Relationship Comments The Item Relationships add-on is installed, and it has been patched to feature relationship comments. Check this if you want Range Search to scan inside relationship comments. Trigger Re-indexing of Existing Content Triple Units You may enter a number in the forms XXXX, XXXX-YY, or XXXX-YY-ZZ, or a number range consisting of two numbers, separated by a hypen ("-"). You may also select one of the units that you defined to limit the search to. Range Search will find items that contain numbers and number ranges matching your search. For example: "500" will find an item mentioning the number range "450-550". [n/a] … (Range) Project-Id-Version: WeSa Omeka
Report-Msgid-Bugs-To: http://github.com/GerZah/plugin-RangeSearch/issues
POT-Creation-Date: 2012-01-09 21:49-0500
PO-Revision-Date: 2016-02-10 16:19+0100
Last-Translator: Gero Zahn <gerozahn@campus.uni-paderborn.de>
Language-Team: German (Germany) (http://www.transifex.com/upb/wesa-omeka/language/de_DE/)
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
Language: de_DE
Plural-Forms: nplurals=2; plural=(n != 1);
X-Generator: Poedit 1.8.7
 
<p>
Um ein Einheiten-Tripel zu definieren, verwenden Sie bitte die Form „a-b-c“, z.B. so:
<pre>
yd-ft-in
m-cm-mm
</pre>
</p>
<p>
Sie können darüber hinaus die hierarchischen Umrechnungsfaktoren zwischen den drei Einzel-Einheiten spezifizieren. Setzen Sie diese hierzu in runden Klammern hinter das Einheiten-Tripel, z.B. so:
<pre>
yd-ft-in (1-3-12)
m-cm-mm (1-100-10)
</pre>
Damit hätten Sie definiert, dass (a) 1 Yard 3 Fuß entspricht, während 1 Fuß 12 Zoll entspricht, sowie dass (b) 1 Meter 100 Zentimetern entspricht, während 1 Zentimeter 10 Millimetern entspricht. — Offensichtlich muss die erste Zahl innerhalb der runden Klammern immer „1“ sein.
</p>
<p>
Zusätzlich können Sie die verschiedenen Einheiten-Tripel in Kategorien gruppieren. Setzen Sie hierzu einen Kategorien-Namen in eckige Klammern vor das Einheiten-Tripel, z.B. so:
<pre>
[Imperial] mi-yd-ft (1-1760-3)
[Imperial] yd-ft-in (1-3-12)
[Metrisch] km-m-cm (1-1000-100)
[Metrisch] m-cm-mm (1-100-10)
</pre>
<em>Bitte beachten Sie:</em> Für die Zuweisung eines Gruppen-Titels ist es nicht zwingend erforderlich, Umrechnungsfaktoren zu spezifizieren.
</p>
<p>
Sowohl „Yard“ / „Fuß“ als auch „Meter“ / „Zentimeter“ wurden hier jeweils zweimal in zwei unterschiedlichen Einheiten-Tripeln spezifiziert. Innerhalb einer Gruppe von Einheiten-Tripeln stellt Bereichs-Suche automatisch den semantischen Zusammenhang zwischen identischen Einzel-Einheiten her, so dass Sie zwischen ihnen konvertieren können, basierend auf den jeweiligen Umrechnungsfaktoren.
</p>
<hr>
<p>
Um Zahlen oder Bereiche (daher der Name Bereichs-Suche) in Metadaten-Felder einzugeben, können Sie das Pop-Up-Tool verwenden, das Sie vom Objekteditor aus erreichen können. Sie können diese auch händisch in der unten angegebenen Form eingeben, indem Sie konkrete Zahlen mit den Einheiten-Namen verwenden, z.B. so:
<pre>
1yd-2ft-3in
1m-50cm - 2m
</pre>
Wie Sie sehen können, können Sie die letzten ein oder zwei Zahlen und Einheiten auslassen.<br>
<em>Bitte beachten Sie:</em> Die erste Zahl (d.h. die höchstwertige Einheit) kann bis zu zehn Ziffern lang sein, während die zweite und dritte Zahl (d.h. die beiden niedrigerwertigen Einheiten) nur jeweils bis zu vier Ziffern lang sein können.
</p>
             <em>Erläuterung:</em> Die Bereichs-Suche stützt sich auf einen Suchindex, der während der Inhaltspflege im Hintergrund erstellt wird. Jedoch werden vorhandene Inhalte nicht automatisch erneut indexiert. Sofern Ihre Datenbank also über bestehende Inhalte verfügt, oder wenn Sie Ihre Einstellungen ändern, sollten Sie den Suchindex neu generieren. <strong>Bitte beachten:</strong> Wenn Sie diese Option auswählen, wird der Index <em>jetzt</em> und genau <em>einmal</em>neu generiert. Diese Aktion wird durchgeführt, sobald Sie auf „Änderungen speichern“ klicken. Anwenden Automatische Umrechnungen Wählen Sie dies aus, wenn Sie Zahlen oder Bereiche in allen Textfeldern eines Objektes suchen wollen. Umrechnungssatz Umrechnen Debug-Ausgabe Eingabe Suche auf Felder beschränken Bitte klicken Sie hier, um zusätzliche Informationen ein-/auszublenden. Bitte geben Sie eine Zahl ein. Bitte geben Sie alle Einheiten-Tripel ein, die Sie unterstützen wollen, eine pro Zeile. Bitte wählen Sie zuerst ein Eingabefeld als Ziel. Bitte wählen Sie eine Einheit. Bitte wählen Sie die Elemente bzw. Felder, auf die die Suche nach Nummern beschränkt sein soll.<br><em>Bitte beachten Sie:</em> Um mehrere Einträge auszuwählen, halten Sie bitte während des Klickens die Strg-Taste (Windows) bzw. die Command-Taste (Mac) gedrückt. Bereichseingabe Bereichs-Suche Bereichs-Suche Debug-Ausgabe Alle Textfelder durchsuchen Innerhalb von Objekt-Beziehungs-Kommentaren suchen Das Objekt-Beziehungen (Item Relationships) Add-on ist installiert, und es ist gepatcht, um Beziehungs-Kommentare zu unterstützen. Wählen Sie dies aus, wenn die Suche innerhalb der Beziehungskommentare erfolgen soll. Re-Indexierung von bestehendem Inhalt auslösen Einheiten-Tripel Sie können eine Zahl in der Form XXXX, XXXX-YY oder XXXX-YY-ZZ eingeben, oder einen Zahlenbereich, bestehend aus zwei Zahlen, getrennt durch einen Bindestrich("-"). Darüber hinaus können Sie eine von Ihnen gewählte Einheit auswählen, um die Suche zu begrenzen. Die Bereichs-Suche findet Objekte, die Zahlen oder Zahlenbereiche enthalten, die zur Suche passen. Beispielweise wird die Suche nach "500" ein Objekt finden, das den Zahlenbereich "450-550" enthält. [n.v.] … (Bereich) 