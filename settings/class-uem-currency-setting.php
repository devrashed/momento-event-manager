<?php

/**
 * 
 * Currency setting
 * 
 */

class Class_currency_setting {

    public function __construct() { 

        add_action('admin_init', [$this, 'webcu_event_save_currency']);
    }

    public function webcu_event_currency_fields() {
        // Process save immediately in case constructor hook wasn't registered early enough
        if ( empty( $_POST ) === false ) {
            $this->webcu_event_save_currency();
        }
        ?>

        <div class="my-currency-settings">
            <form method="POST" id="myCurrencyForm">
                <?php wp_nonce_field('webcu_currency_action', 'webcu_currency_nonce'); ?>
                <?php $my_currency =  get_option('my_currency'); ?>

                <label><?php esc_html_e( 'Currency', 'mega-events-manager' ); ?></label>
                <select name="currency" id="currency">
                 <option value="AED" <?php if ($my_currency =='') { echo 'selected'; } ?> >United Arab Emirates dirham (د.إ) — AED</option>
                        <option value="AFN" <?php if ($my_currency=='AFN') { echo 'selected'; } ?> >Afghan afghani (؋) — AFN</option>
                        <option value="ALL" <?php if ($my_currency=='ALL') { echo 'selected'; } ?> >Albanian lek (L) — ALL</option>
                        <option value="AMD" <?php if ($my_currency=='AMD') { echo 'selected'; } ?> >Armenian dram (AMD) — AMD</option>
                        <option value="ANG" <?php if ($my_currency=='ANG') { echo 'selected'; } ?> >Netherlands Antillean guilder (ƒ) — ANG</option>
                        <option value="AOA" <?php if ($my_currency=='AOA') { echo 'selected'; } ?> >Angolan kwanza (Kz) — AOA</option>
                        <option value="ARS" <?php if ($my_currency=='ARS') { echo 'selected'; } ?> >Argentine peso ($) — ARS</option>
                        <option value="AUD" <?php if ($my_currency=='AUD') { echo 'selected'; } ?> >Australian dollar ($) — AUD</option>
                        <option value="AWG" <?php if ($my_currency=='AWG') { echo 'selected'; } ?> >Aruban florin (Afl.) — AWG</option>
                        <option value="AZN" <?php if ($my_currency=='AZN') { echo 'selected'; } ?> >Azerbaijani manat (₼) — AZN</option>
                        <option value="BAM" <?php if ($my_currency=='BAM') { echo 'selected'; } ?> >Bosnia and Herzegovina convertible mark (KM) — BAM</option>
                        <option value="BBD" <?php if ($my_currency=='BBD') { echo 'selected'; } ?> >Barbadian dollar ($) — BBD</option>
                        <option value="BDT" <?php if ($my_currency=='BDT') { echo 'selected'; } ?> >Bangladeshi taka (৳&nbsp;) — BDT</option>
                        <option value="BGN" <?php if ($my_currency=='BGN') { echo 'selected'; } ?> >Bulgarian lev (лв.) — BGN</option>
                        <option value="BHD" <?php if ($my_currency=='BHD') { echo 'selected'; } ?> >Bahraini dinar (.د.ب) — BHD</option>
                        <option value="BIF" <?php if ($my_currency=='BIF') { echo 'selected'; } ?> >Burundian franc (Fr) — BIF</option>
                        <option value="BMD" <?php if ($my_currency=='BMD') { echo 'selected'; } ?> >Bermudian dollar ($) — BMD</option>
                        <option value="BND" <?php if ($my_currency=='BND') { echo 'selected'; } ?> >Brunei dollar ($) — BND</option>
                        <option value="BOB" <?php if ($my_currency=='BOB') { echo 'selected'; } ?> >Bolivian boliviano (Bs.) — BOB</option>
                        <option value="BRL" <?php if ($my_currency=='BRL') { echo 'selected'; } ?> >Brazilian real (R$) — BRL</option>
                        <option value="BSD" <?php if ($my_currency=='BSD') { echo 'selected'; } ?> >Bahamian dollar ($) — BSD</option>
                        <option value="BTC" <?php if ($my_currency=='BTC') { echo 'selected'; } ?> >Bitcoin (฿) — BTC</option>
                        <option value="BTN" <?php if ($my_currency=='BTN') { echo 'selected'; } ?> >Bhutanese ngultrum (Nu.) — BTN</option>
                        <option value="BWP" <?php if ($my_currency=='BWP') { echo 'selected'; } ?> >Botswana pula (P) — BWP</option>
                        <option value="BYR" <?php if ($my_currency=='BYR') { echo 'selected'; } ?> >Belarusian ruble (old) (Br) — BYR</option>
                        <option value="BYN" <?php if ($my_currency=='BYN') { echo 'selected'; } ?> >Belarusian ruble (Br) — BYN</option>
                        <option value="BZD" <?php if ($my_currency=='BZD') { echo 'selected'; } ?> >Belize dollar ($) — BZD</option>
                        <option value="CAD" <?php if ($my_currency=='CAD') { echo 'selected'; } ?> >Canadian dollar ($) — CAD</option>
                        <option value="CDF" <?php if ($my_currency=='CDF') { echo 'selected'; } ?> >Congolese franc (Fr) — CDF</option>
                        <option value="CHF" <?php if ($my_currency=='CHF') { echo 'selected'; } ?> >Swiss franc (CHF) — CHF</option>
                        <option value="CLP" <?php if ($my_currency=='CLP') { echo 'selected'; } ?> >Chilean peso ($) — CLP</option>
                        <option value="CNY" <?php if ($my_currency=='CNY') { echo 'selected'; } ?> >Chinese yuan (¥) — CNY</option>
                        <option value="COP" <?php if ($my_currency=='COP') { echo 'selected'; } ?> >Colombian peso ($) — COP</option>
                        <option value="CRC" <?php if ($my_currency=='CRC') { echo 'selected'; } ?> >Costa Rican colón (₡) — CRC</option>
                        <option value="CUC" <?php if ($my_currency=='CUC') { echo 'selected'; } ?> >Cuban convertible peso ($) — CUC</option>
                        <option value="CUP" <?php if ($my_currency=='CUP') { echo 'selected'; } ?> >Cuban peso ($) — CUP</option>
                        <option value="CVE" <?php if ($my_currency=='CVE') { echo 'selected'; } ?> >Cape Verdean escudo ($) — CVE</option>
                        <option value="CZK" <?php if ($my_currency=='CZK') { echo 'selected'; } ?> >Czech koruna (Kč) — CZK</option>
                        <option value="DJF" <?php if ($my_currency=='DJF') { echo 'selected'; } ?> >Djiboutian franc (Fr) — DJF</option>
                        <option value="DKK" <?php if ($my_currency=='DKK') { echo 'selected'; } ?> >Danish krone (kr.) — DKK</option>
                        <option value="DOP" <?php if ($my_currency=='DOP') { echo 'selected'; } ?> >Dominican peso (RD$) — DOP</option>
                        <option value="DZD" <?php if ($my_currency=='DZD') { echo 'selected'; } ?> >Algerian dinar (د.ج) — DZD</option>
                        <option value="EGP" <?php if ($my_currency=='EGP') { echo 'selected'; } ?> >Egyptian pound (EGP) — EGP</option>
                        <option value="ERN" <?php if ($my_currency=='ERN') { echo 'selected'; } ?> >Eritrean nakfa (Nfk) — ERN</option>
                        <option value="ETB" <?php if ($my_currency=='ETB') { echo 'selected'; } ?> >Ethiopian birr (Br) — ETB</option>
                        <option value="EUR" <?php if ($my_currency=='EUR') { echo 'selected'; } ?> >Euro (€) — EUR</option>
                        <option value="FJD" <?php if ($my_currency=='FJD') { echo 'selected'; } ?> >Fijian dollar ($) — FJD</option>
                        <option value="FKP" <?php if ($my_currency=='FKP') { echo 'selected'; } ?> >Falkland Islands pound (£) — FKP</option>
                        <option value="GBP" <?php if ($my_currency=='GBP') { echo 'selected'; } ?> >Pound sterling (£) — GBP</option>
                        <option value="GEL" <?php if ($my_currency=='GEL') { echo 'selected'; } ?> >Georgian lari (₾) — GEL</option>
                        <option value="GGP" <?php if ($my_currency=='GGP') { echo 'selected'; } ?> >Guernsey pound (£) — GGP</option>
                        <option value="GHS" <?php if ($my_currency=='GHS') { echo 'selected'; } ?> >Ghana cedi (₵) — GHS</option>
                        <option value="GIP" <?php if ($my_currency=='GIP') { echo 'selected'; } ?> >Gibraltar pound (£) — GIP</option>
                        <option value="GMD" <?php if ($my_currency=='GMD') { echo 'selected'; } ?> >Gambian dalasi (D) — GMD</option>
                        <option value="GNF" <?php if ($my_currency=='GNF') { echo 'selected'; } ?> >Guinean franc (Fr) — GNF</option>
                        <option value="GTQ" <?php if ($my_currency=='GTQ') { echo 'selected'; } ?> >Guatemalan quetzal (Q) — GTQ</option>
                        <option value="GYD" <?php if ($my_currency=='GYD') { echo 'selected'; } ?> >Guyanese dollar ($) — GYD</option>
                        <option value="HKD" <?php if ($my_currency=='HKD') { echo 'selected'; } ?> >Hong Kong dollar ($) — HKD</option>
                        <option value="HNL" <?php if ($my_currency=='HNL') { echo 'selected'; } ?> >Honduran lempira (L) — HNL</option>
                        <option value="HRK" <?php if ($my_currency=='HRK') { echo 'selected'; } ?> >Croatian kuna (kn) — HRK</option>
                        <option value="HTG" <?php if ($my_currency=='HTG') { echo 'selected'; } ?> >Haitian gourde (G) — HTG</option>
                        <option value="HUF" <?php if ($my_currency=='HUF') { echo 'selected'; } ?> >Hungarian forint (Ft) — HUF</option>
                        <option value="IDR" <?php if ($my_currency=='IDR') { echo 'selected'; } ?> >Indonesian rupiah (Rp) — IDR</option>
                        <option value="ILS" <?php if ($my_currency=='ILS') { echo 'selected'; } ?> >Israeli new shekel (₪) — ILS</option>
                        <option value="IMP" <?php if ($my_currency=='IMP') { echo 'selected'; } ?> >Manx pound (£) — IMP</option>
                        <option value="INR" <?php if ($my_currency=='INR') { echo 'selected'; } ?> >Indian rupee (₹) — INR</option>
                        <option value="IQD" <?php if ($my_currency=='IQD') { echo 'selected'; } ?> >Iraqi dinar (د.ع) — IQD</option>
                        <option value="IRR" <?php if ($my_currency=='IRR') { echo 'selected'; } ?> >Iranian rial (﷼) — IRR</option>
                        <option value="IRT" <?php if ($my_currency=='IRT') { echo 'selected'; } ?> >Iranian toman (تومان) — IRT</option>
                        <option value="ISK" <?php if ($my_currency=='ISK') { echo 'selected'; } ?> >Icelandic króna (kr.) — ISK</option>
                        <option value="JEP" <?php if ($my_currency=='JEP') { echo 'selected'; } ?> >Jersey pound (£) — JEP</option>
                        <option value="JMD" <?php if ($my_currency=='JMD') { echo 'selected'; } ?> >Jamaican dollar ($) — JMD</option>
                        <option value="JOD" <?php if ($my_currency=='JOD') { echo 'selected'; } ?> >Jordanian dinar (د.ا) — JOD</option>
                        <option value="JPY" <?php if ($my_currency=='JPY') { echo 'selected'; } ?> >Japanese yen (¥) — JPY</option>
                        <option value="KES" <?php if ($my_currency=='KES') { echo 'selected'; } ?>>Kenyan shilling (KSh) — KES</option>
                        <option value="KGS" <?php if ($my_currency=='KGS') { echo 'selected'; } ?>>Kyrgyzstani som (сом) — KGS</option>
                        <option value="KHR" <?php if ($my_currency=='KHR') { echo 'selected'; } ?>>Cambodian riel (៛) — KHR</option>
                        <option value="KMF" <?php if ($my_currency=='KMF') { echo 'selected'; } ?>>Comorian franc (Fr) — KMF</option>
                        <option value="KPW" <?php if ($my_currency=='KPW') { echo 'selected'; } ?>>North Korean won (₩) — KPW</option>
                        <option value="KRW" <?php if ($my_currency=='KRW') { echo 'selected'; } ?>>South Korean won (₩) — KRW</option>
                        <option value="KWD" <?php if ($my_currency=='KWD') { echo 'selected'; } ?>>Kuwaiti dinar (د.ك) — KWD</option>
                        <option value="KYD" <?php if ($my_currency=='KYD') { echo 'selected'; } ?>>Cayman Islands dollar ($) — KYD</option>
                        <option value="KZT" <?php if ($my_currency=='KZT') { echo 'selected'; } ?>>Kazakhstani tenge (₸) — KZT</option>
                        <option value="LAK" <?php if ($my_currency=='LAK') { echo 'selected'; } ?>>Lao kip (₭) — LAK</option>
                        <option value="LBP" <?php if ($my_currency=='LBP') { echo 'selected'; } ?>>Lebanese pound (ل.ل) — LBP</option>
                        <option value="LKR" <?php if ($my_currency=='LKR') { echo 'selected'; } ?>>Sri Lankan rupee (රු) — LKR</option>
                        <option value="LRD" <?php if ($my_currency=='LRD') { echo 'selected'; } ?>>Liberian dollar ($) — LRD</option>
                        <option value="LSL" <?php if ($my_currency=='LSL') { echo 'selected'; } ?>>Lesotho loti (L) — LSL</option>
                        <option value="LYD" <?php if ($my_currency=='LYD') { echo 'selected'; } ?>>Libyan dinar (د.ل) — LYD</option>
                        <option value="MAD" <?php if ($my_currency=='MAD') { echo 'selected'; } ?>>Moroccan dirham (د.م.) — MAD</option>
                        <option value="MDL" <?php if ($my_currency=='MDL') { echo 'selected'; } ?>>Moldovan leu (MDL) — MDL</option>
                        <option value="MGA" <?php if ($my_currency=='MGA') { echo 'selected'; } ?>>Malagasy ariary (Ar) — MGA</option>
                        <option value="MKD" <?php if ($my_currency=='MKD') { echo 'selected'; } ?>>Macedonian denar (ден) — MKD</option>
                        <option value="MMK" <?php if ($my_currency=='MMK') { echo 'selected'; } ?>>Burmese kyat (Ks) — MMK</option>
                        <option value="MNT" <?php if ($my_currency=='MNT') { echo 'selected'; } ?>>Mongolian tögrög (₮) — MNT</option>
                        <option value="MOP" <?php if ($my_currency=='MOP') { echo 'selected'; } ?>>Macanese pataca (P) — MOP</option>
                        <option value="MRU" <?php if ($my_currency=='MRU') { echo 'selected'; } ?>>Mauritanian ouguiya (UM) — MRU</option>
                        <option value="MUR" <?php if ($my_currency=='MUR') { echo 'selected'; } ?>>Mauritian rupee (₨) — MUR</option>
                        <option value="MVR" <?php if ($my_currency=='MVR') { echo 'selected'; } ?>>Maldivian rufiyaa (.ރ) — MVR</option>
                        <option value="MWK" <?php if ($my_currency=='MWK') { echo 'selected'; } ?>>Malawian kwacha (MK) — MWK</option>
                        <option value="MXN" <?php if ($my_currency=='MXN') { echo 'selected'; } ?>>Mexican peso ($) — MXN</option>
                        <option value="MYR" <?php if ($my_currency=='MYR') { echo 'selected'; } ?>>Malaysian ringgit (RM) — MYR</option>
                        <option value="MZN" <?php if ($my_currency=='MZN') { echo 'selected'; } ?>>Mozambican metical (MT) — MZN</option>
                        <option value="NAD" <?php if ($my_currency=='NAD') { echo 'selected'; } ?>>Namibian dollar (N$) — NAD</option>
                        <option value="NGN" <?php if ($my_currency=='NGN') { echo 'selected'; } ?>>Nigerian naira (₦) — NGN</option>
                        <option value="NIO" <?php if ($my_currency=='NIO') { echo 'selected'; } ?>>Nicaraguan córdoba (C$) — NIO</option>
                        <option value="NOK" <?php if ($my_currency=='NOK') { echo 'selected'; } ?>>Norwegian krone (kr) — NOK</option>
                        <option value="NPR" <?php if ($my_currency=='NPR') { echo 'selected'; } ?>>Nepalese rupee (₨) — NPR</option>
                        <option value="NZD" <?php if ($my_currency=='NZD') { echo 'selected'; } ?>>New Zealand dollar ($) — NZD</option>
                        <option value="OMR" <?php if ($my_currency=='OMR') { echo 'selected'; } ?>>Omani rial (ر.ع.) — OMR</option>
                        <option value="PAB" <?php if ($my_currency=='PAB') { echo 'selected'; } ?>>Panamanian balboa (B/.) — PAB</option>
                        <option value="PEN" <?php if ($my_currency=='PEN') { echo 'selected'; } ?>>Sol (S/) — PEN</option>
                        <option value="PGK" <?php if ($my_currency=='PGK') { echo 'selected'; } ?>>Papua New Guinean kina (K) — PGK</option>
                        <option value="PHP" <?php if ($my_currency=='PHP') { echo 'selected'; } ?>>Philippine peso (₱) — PHP</option>
                        <option value="PKR" <?php if ($my_currency=='PKR') { echo 'selected'; } ?>>Pakistani rupee (₨) — PKR</option>
                        <option value="PLN" <?php if ($my_currency=='PLN') { echo 'selected'; } ?>>Polish złoty (zł) — PLN</option>
                        <option value="PRB" <?php if ($my_currency=='PRB') { echo 'selected'; } ?>>Transnistrian ruble (р.) — PRB</option>
                        <option value="PYG" <?php if ($my_currency=='PYG') { echo 'selected'; } ?>>Paraguayan guaraní (₲) — PYG</option>
                        <option value="QAR" <?php if ($my_currency=='QAR') { echo 'selected'; } ?>>Qatari riyal (ر.ق) — QAR</option>
                        <option value="RON" <?php if ($my_currency=='RON') { echo 'selected'; } ?>>Romanian leu (lei) — RON</option>
                        <option value="RSD" <?php if ($my_currency=='RSD') { echo 'selected'; } ?>>Serbian dinar (рсд) — RSD</option>
                        <option value="RUB" <?php if ($my_currency=='RUB') { echo 'selected'; } ?>>Russian ruble (₽) — RUB</option>
                        <option value="RWF" <?php if ($my_currency=='RWF') { echo 'selected'; } ?>>Rwandan franc (Fr) — RWF</option>
                        <option value="SAR" <?php if ($my_currency=='SAR') { echo 'selected'; } ?>>Saudi riyal (ر.س) — SAR</option>
                        <option value="SBD" <?php if ($my_currency=='SBD') { echo 'selected'; } ?>>Solomon Islands dollar ($) — SBD</option>
                        <option value="SCR" <?php if ($my_currency=='SCR') { echo 'selected'; } ?>>Seychellois rupee (₨) — SCR</option>
                        <option value="SDG" <?php if ($my_currency=='SDG') { echo 'selected'; } ?>>Sudanese pound (ج.س.) — SDG</option>
                        <option value="SEK" <?php if ($my_currency=='SEK') { echo 'selected'; } ?>>Swedish krona (kr) — SEK</option>
                        <option value="SGD" <?php if ($my_currency=='SGD') { echo 'selected'; } ?>>Singapore dollar ($) — SGD</option>
                        <option value="SHP" <?php if ($my_currency=='SHP') { echo 'selected'; } ?>>Saint Helena pound (£) — SHP</option>
                        <option value="SLL" <?php if ($my_currency=='SLL') { echo 'selected'; } ?>>Sierra Leonean leone (Le) — SLL</option>
                        <option value="SOS" <?php if ($my_currency=='SOS') { echo 'selected'; } ?>>Somali shilling (Sh) — SOS</option>
                        <option value="SRD" <?php if ($my_currency=='SRD') { echo 'selected'; } ?>>Surinamese dollar ($) — SRD</option>
                        <option value="SSP" <?php if ($my_currency=='SSP') { echo 'selected'; } ?>>South Sudanese pound (£) — SSP</option>
                        <option value="STN" <?php if ($my_currency=='STN') { echo 'selected'; } ?>>São Tomé and Príncipe dobra (Db) — STN</option>
                        <option value="SYP" <?php if ($my_currency=='SYP') { echo 'selected'; } ?>>Syrian pound (ل.س) — SYP</option>
                        <option value="SZL" <?php if ($my_currency=='SZL') { echo 'selected'; } ?>>Swazi lilangeni (E) — SZL</option>
                        <option value="THB" <?php if ($my_currency=='THB') { echo 'selected'; } ?>>Thai baht (฿) — THB</option>
                        <option value="TJS" <?php if ($my_currency=='TJS') { echo 'selected'; } ?>>Tajikistani somoni (ЅМ) — TJS</option>
                        <option value="TMT" <?php if ($my_currency=='TMT') { echo 'selected'; } ?>>Turkmenistan manat (m) — TMT</option>
                        <option value="TND" <?php if ($my_currency=='TND') { echo 'selected'; } ?>>Tunisian dinar (د.ت) — TND</option>
                        <option value="TOP" <?php if ($my_currency=='TOP') { echo 'selected'; } ?>>Tongan paʻanga (T$) — TOP</option>
                        <option value="TRY" <?php if ($my_currency=='TRY') { echo 'selected'; } ?>>Turkish lira (₺) — TRY</option>
                        <option value="TTD" <?php if ($my_currency=='TTD') { echo 'selected'; } ?>>Trinidad and Tobago dollar ($) — TTD</option>
                        <option value="TWD" <?php if ($my_currency=='TWD') { echo 'selected'; } ?>>New Taiwan dollar (NT$) — TWD</option>
                        <option value="TZS" <?php if ($my_currency=='TZS') { echo 'selected'; } ?>>Tanzanian shilling (Sh) — TZS</option>
                        <option value="UAH" <?php if ($my_currency=='UAH') { echo 'selected'; } ?>>Ukrainian hryvnia (₴) — UAH</option>
                        <option value="UGX" <?php if ($my_currency=='UGX') { echo 'selected'; } ?>>Ugandan shilling (UGX) — UGX</option>
                        <option value="USD" <?php if ($my_currency=='USD') { echo 'selected'; } ?>>United States (US) dollar ($) — USD</option>
                        <option value="UYU" <?php if ($my_currency=='UYU') { echo 'selected'; } ?>>Uruguayan peso ($) — UYU</option>
                        <option value="UZS" <?php if ($my_currency=='UZS') { echo 'selected'; } ?>>Uzbekistani som (UZS) — UZS</option>
                        <option value="VEF" <?php if ($my_currency=='VEF') { echo 'selected'; } ?>>Venezuelan bolívar (2008–2018) (Bs F) — VEF</option>
                        <option value="VES" <?php if ($my_currency=='VES') { echo 'selected'; } ?>>Venezuelan bolívar (Bs.) — VES</option>
                        <option value="VND" <?php if ($my_currency=='VND') { echo 'selected'; } ?>>Vietnamese đồng (₫) — VND</option>
                        <option value="VUV" <?php if ($my_currency=='VUV') { echo 'selected'; } ?>>Vanuatu vatu (Vt) — VUV</option>
                        <option value="WST" <?php if ($my_currency=='WST') { echo 'selected'; } ?>>Samoan tālā (T) — WST</option>
                        <option value="XAF" <?php if ($my_currency=='XAF') { echo 'selected'; } ?>>Central African CFA franc (CFA) — XAF</option>
                        <option value="XCD" <?php if ($my_currency=='XCD') { echo 'selected'; } ?>>East Caribbean dollar ($) — XCD</option>
                        <option value="XOF" <?php if ($my_currency=='XOF') { echo 'selected'; } ?>>West African CFA franc (CFA) — XOF</option>
                        <option value="XPF" <?php if ($my_currency=='XPF') { echo 'selected'; } ?>>CFP franc (XPF) — XPF</option>
                        <option value="YER" <?php if ($my_currency=='YER') { echo 'selected'; } ?>>Yemeni rial (﷼) — YER</option>
                        <option value="ZAR" <?php if ($my_currency=='ZAR') { echo 'selected'; } ?>>South African rand (R) — ZAR</option>
                        <option value="ZMW" <?php if ($my_currency=='ZMW') { echo 'selected'; } ?>>Zambian kwacha (ZK) — ZMW</option>
                </select>

                <label><?php esc_html_e( 'Currency Position', 'mega-events-manager' ); ?></label>
                <?php $curr_opsition = get_option('my_currency_position'); ?>
                <select name="currency_position" id="currency_position">
                    <option value="left" <?php if ($curr_opsition=='left') { echo 'selected'; } ?>><?php esc_html_e( 'Left', 'mega-events-manager' ); ?></option>
                    <option value="right" <?php if ($curr_opsition=='right') { echo 'selected'; } ?>> <?php esc_html_e( 'Right', 'mega-events-manager' ); ?></option>
                    <option value="left_space" <?php if ($curr_opsition=='left_space') { echo 'selected'; } ?>><?php esc_html_e( 'Left Space', 'mega-events-manager' ); ?></option>
                    <option value="right_space" <?php if ($curr_opsition=='right_space') { echo 'selected'; } ?>><?php esc_html_e( 'Right Space', 'mega-events-manager' ); ?></option>
                </select>

                <label><?php esc_html_e( 'Thousand Separator', 'mega-events-manager' ); ?></label>
                <input type="text" name="thousand_separator" id="thousand_separator" value="<?php echo get_option('my_thousand_separator'); ?>" placeholder=",">

                <label><?php esc_html_e( 'Decimal Separator', 'mega-events-manager' ); ?></label>
                <input type="text" name="decimal_separator" id="decimal_separator" value="<?php echo get_option('my_decimal_separator'); ?>" placeholder=".">

                <label><?php esc_html_e( 'Number of Decimals', 'mega-events-manager' ); ?></label>
                <input type="number" name="num_decimals" id="num_decimals" value="<?php echo get_option('num_decimals'); ?>"  placeholder="2">

                <button type="submit" name="webcu_currency" class="save-settings">
                    <?php esc_html_e( 'Save Changes', 'mega-events-manager' ); ?>
                </button>

            </form>
            <div id="saveMessage"></div>
        </div>

        <?php
    }

    public function webcu_event_save_currency() {

        if ( isset($_POST['webcu_currency']) ) {
            if (!isset($_POST['webcu_currency_nonce']) || !wp_verify_nonce($_POST['webcu_currency_nonce'], 'webcu_currency_action')) {
                return;
            }

            // Sanitize values
            $currency          = sanitize_text_field($_POST['currency']);
            $currency_position = sanitize_text_field($_POST['currency_position']);
            $thou_separet      = sanitize_text_field($_POST['thousand_separator']);
            $decima_separet    = sanitize_text_field($_POST['decimal_separator']);
            $num_decimals    = sanitize_text_field($_POST['num_decimals']);

            // Save
            update_option('my_currency', $currency);
            update_option('my_currency_position', $currency_position);
            update_option('my_thousand_separator', $thou_separet);
            update_option('my_decimal_separator', $decima_separet);
            update_option('num_decimals', $num_decimals);
            
            add_action('admin_notices', function(){
                echo '<div class="updated"><p>Currency settings saved successfully!</p></div>';
            });
        }
    }
} 