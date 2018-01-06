=== Horoscop ===
Contributors: vladwtz
Donate link: https://paypal.me/vladutilie/10
Tags: horoscop, zodiac, zodii, horoscope, star, sign
Requires at least: 2.8.5
Tested up to: 4.9.1
Requires PHP: 5.2.4
Stable tag: 3.9.3
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Probabil cel mai bun modul pentru horoscop pe site-ul tău.

== Description ==
Horoscop oferă o interfață foarte interactivă și prietenoasă cu utilizatorul. Activarea, plasarea în sidebar și utilizarea lui sunt foarte simple.

După activarea modulului, din meniul cu piese (Appearance -> Widgets), acesta se va poziționa în sidebar-ul dorit și se va configura. Setările sunt intuitive și ușor de configurat.

Informațiile sunt preluate de pe siteul [www.acvaria.com](http://www.acvaria.com), cu acordul autorului (mai multe vezi în fișierul horoscop.php), memorate în baza de date și actualizate la intervale scurte de timp.

Dacă îți place ce fac:
* poți face o [donație](https://paypal.me/vladutilie/10) pentru a motiva susținerea proiectului;
* poți face un pull-request la [proiect pe github.com](https://github.com/vladutilie/horoscop) dacă vrei să aduci îmbunătățiri;
* sau mă poți [contacta](http://vladilie.ro/contact) pentru a colabora :)

== Installation ==
Fie prin FTP:
1. Se descarcă modulul și se dezarhivează fișierul Horoscop.zip;
2. Folderul dezarhivat se uploadează pe server în locația `/wp-content/plugins/`;
3. Din meniul Plugins se activează „Horoscop”.
4. Se accesează meniul Appearance > Widgets și prin metoda `Drag and Drop` se plasează widget-ul `Horoscop` în sidebar-ul dorit.
5. Se glisează în jos setările și se configurează după preferințe.

Fie din panoul de control Wordpress:
1. Din meniul Plugins > Add New > căutare Horoscop > click pe Install Now la pluginul „Horoscop”;
2. Click pe Activate.
3. Se accesează meniul Appearance > Widgets și prin metoda `Drag and Drop` se plasează widget-ul `Horoscop` în sidebar-ul dorit.
4. Se glisează în jos setările și se configurează după preferințe.

== Frequently Asked Questions ==

= De ce în textul zodiei apare „Conținutul acestei zodii nu a fost actualizat corespunzător.”? =
Există posiblitatea ca resursele Acvaria să nu fi oferit informațiile necesare, în momentul în care s-au făcut actualizările, pentru ca modulul să poată prelua informațiile.

= Pentru alte probleme =
Te rog [contactează](http://vladilie.ro/contact) autorul și îți va face cinste cu o bere! ;)

== Screenshots ==
1. Se folosește metoda `drag and drop` pentru poziționarea piesei.
2. Se glisează în jos opțiunile piesei și se setează titlul și modul de afișare.
3. Afișarea zodiacului.


== Changelog ==

= 3.9.3 =
* 05/01/2018 | New features. Happy New Year!
* S-au adăugat noi efecte de animare pentru citirea zodiilor.
* S-a îmbunătățit securitatea plugin-ului.
* Acum plugin-ul poate fi tradus și în alte limbi.
* S-au făcut îmbunătățiri în ceea ce privește optimizarea.
* S-a schimbat metoda de preluare a informațiilor de la acvaria.com.
* Codul a fost adaptat la [Standardul de Codare Wordpress](https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/)
* S-a făcut CSS Minify.
* Plugin-ul a fost trecut la licență GPLv3.
* Informațiile despre autor din readme au fost actualizate.

= 2.5.6 =
* 21/05/2017 | Compatibilitate cu noua versiune de Wordpress.
* S-a eliminat un bug ce încurca meniul cu Piese.
* S-a eliminat un bug ce punea probleme la actualizarea informațiilor ce sunt preluate din surse Acvaria.
* S-a actualizat versiunea bibliotecii Google jQuery.
* S-a explicitat eroarea la afișarea textului pentru zodii, în cazul în care informațiile nu pot fi preluate de la Acvaria.
* S-a testat funcționalitatea cu noua versiune Wordpress 4.0+.
* S-au modificate mici părți de texte și cod (readaptări la standarde de programare).
* S-au optimizat tipurile de date ale câmpurilor din baza de date.
* S-a modificat metoda de actualizare periodică și s-a introdus WP-Cron.

= 2.2.9 =
* 04/09/2014 | Wordpress 4.0 ships today!
* S-au efectuat verificări pentru ca totul să funcționeze la parametri normali pe noua versiune de Wordpress, 4.0.
* S-au schimbat mici părți ce țin de standardul de programare și optimizarea codului.
* Actualizarea informațiilor se face acum și în ziua următoare ultimului update.

= 2.1.8 =
* 30/07/2014 | Un pas mare spre implementarea jQuery.
* S-a renunțat la pop-up și s-a introdus afișarea zodiacului direct în site printr-o metodă de slide prin jQuery.
* A fost elaborată o soluție pentru actualizarea informațiilor, astfel încât să nu se facă cereri la resursele Acvaria de fiecare dată când se dorește citirea zodiacului.
* Pe lângă această metodă, au fost create alte 4 funcții și un tabel pentru baza de date în ceea ce privește buna funcționare și auto actualizare.
* Pentru modul se setează time zone-ul specific regiunii Europa/București.
* A fost modificată descrierea modulului și a altor câteva secțiuni.
* Codul CSS a fost modificat și îmbunătățit.
* Structura codului a fost readaptată la standarde.

= 1.8.3 =
* 04/05/2014 | Probabil ultimul update al modulului - cea mai stabilă versiune.
* A fost introdusă opțiunea de alegere a modului de afișare a zodiilor: matrice de imagini sau listă ordonată cu zodii.
* Au fost traduse lunile anului din limba engleză în limba română.
* Au fost adăugate perioadele din an, pentru fiecare zodie.
* A fost modificat atât design-ul paginii pop-up ce afișează zodiacul cât și structura sa în mică măsură.
* Mici părți de cod au fost adaptate standardelor de programare în PHP.
* A fost limitat riscul apariției uneor erori, în cazul folosirii abuzive a modulului.
* A fost reformatată informația primită de către resursele Acvaria, astfel încât să nu existe caractere inutile ce pot cauza încărcarea îndelungată a paginii pop-up.

= 1.5.8 =
* 01/02/2014 | Primul update din 2014: numai bine în noul an!
* Lista banală cu zodii ce se afișează în sidebar, a fost înlocuită cu imagini sugestive.
* Structura codului a fost revizuită și adaptată standardelor de programare.
* Au fost adăugate informații cu privire la drepturile de autor ale Acvaria(R).
* Au fost schimbate câteva texte din modul și informațiile despre autor.

= 1.4.5 =
* 11/05/2013 | S-a detectat eroarea principală care se tot raporta în fișierul error_log și s-a rezolvat.
* A fost introdus fișierul widget.php care a preluat funcțiile fișierului horoscop.php, devenind fișier de bază al modulului.
* Fișierul horoscop.php a devenit fișier secundar.
* A fost modificată structura codului.
* Funcțiile din clasa `Horoscop` au fost revizuite și îmbunătățite.
* Modulul se poate activa doar cu modulul PHP `allow_url_fopen` funcțional pe serverul de găzduire.
* S-a restricționat accesul direct către modul.
* La dezactivare, se curăță toate informațiile adăugate inițial, de modul, în baza de date.

= 1.0.1 =
* 24/08/2012 | Schimbarea linkului din fereastra pop-up de la citirea zodiacului, de la /wp-content/plugins/horoscop/horoscop.php?zodie=(zodie) la /horoscop/(zodie).

= 1.0.0 =
* August 2012 | Lansare modul.


== Upgrade Notice ==

= 3.9.3 =
05/01/2018 | S-au adus îmbunătățiri majore la nivel de funcționalitate, structură și securitate. A fost introdusă posiblitatea de traducere.

= 2.5.6 =
20/05/2017 | Compatibilitate cu noul Wordpress 4+, fixarea de bug-uri și modificări de finețe.

= 2.2.9 =
04/09/2014 | Pregătiri pentru Wordpress 4.0. Yuhuuu!

= 2.1.8 =
30/07/2014 | S-a renunțat la afișarea zodiacului în pop-up și s-a trecut la afișarea sa direct în site.

= 1.8.3 =
04/05/2014 | Probabil ultimul update. Aceasta este considerată cea mai stabilă versiune.

= 1.5.8 =
01/02/2014 | Lista horoscopului afișată pe site, a fost înlocuită de o listă cu imagini sugestive pentru zodii.

= 1.4.5 =
11/05/2013 | S-au rezolvat buguri și s-au refăcut funcțiile, toate modificările fiind aduse în favoarea optimizării și flexibilității codului.

= 1.0.1 =
24/08/2012 | Simplificarea și omogenizarea linkului din fereastra pop-up de la citirea zodiacului.

= 1.0.0 =
August 2012 | Lansare modul.


== Despre autor ==
Am absolvit Facultatea de Automatică și Calculatoare din cadrul Universității Politehnica Timişoara, iar acum sunt masterand în cadrul Facultății de Științe Economice și Gestiunea Afacerilor de la Universitatea Babeș-Bolyai din Cluj-Napoca.
Sunt pasionat de teatru, muzică, lectură, blogging, programare, călătorii și natură. Poate și alte lucruri simple.

Dacă ai o idee frumoasă la care vrei să lucrăm împreună, dă-mi un semn.
În acest sens mă poți găsi pe:
* [Facebook](https://www.facebook.com/i.vladut)
* [Linkedin](https://www.linkedin.com/in/vladilie/)
* [GoodReads](https://www.goodreads.com/user/show/68128050-vl-du-ilie)
* [Instagram](https://www.instagram.com/vladut.ilie/)
* [Twitter](https://twitter.com/vladilie94)
* [Google+](https://plus.google.com/+Vl%C4%83du%C5%A3Ilie)
* [Gravatar](https://ro.gravatar.com/vladilie94).
sau pe [email](http://vladilie.ro/contact/).

Cu drag aștept! :)
Icons made by [Freepik](https://www.freepik.com/) from [www.flaticon.com](https://www.flaticon.com/)