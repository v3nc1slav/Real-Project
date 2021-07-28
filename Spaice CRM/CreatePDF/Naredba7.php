<?php
 
/**
 * @copyright: 
 * @jira: T-133
 * @author: Ventsislav Verchov 
 */
 
require_once('include/tcpdf/tcpdf.php');
 
function Naredba_7($id)
{
    global $mod_strings, $app_strings, $app_list_strings, $current_user, $db, $timedate;
 
    $bean =  BeanFactory::getBean("BZLNK_Offers", $id);
    $userId = $bean->assigned_user_id;
    $userBean = BeanFactory::getBean("Users", $userId);
 
 
    $bean->load_relationship('bzlnk_offers_contacts');
    $relation = $bean->bzlnk_offers_contacts->get();
 
    if (count($relation) > 0) {
 
        $contactBean = BeanFactory::getBean("Contacts", $relation[0]);
        $contacts = $bean->bzlnk_offers_contacts_name;
        $contact_salutation = $app_list_strings['salutation_dom'][$contactBean->salutation];
    } else {
        $contacts = '....';
        $contact_salutation = 'Г-н / Г-жо ';
    }
 
    $account = $bean->bzlnk_offers_accounts_name;
    $contacts = $bean->bzlnk_offers_contacts_name;
    $email = $userBean->email1;
    $assigned = $bean->assigned_user_name;
    $phone_work = $userBean->phone_work;
    $dateOffers = $bean->dateoffers_c;
    $validityOffer = $bean->validity_offer;
    // Creating timestamp from given date
    $timestamp = strtotime($dateOffers);
    // Creating new date format from that timestamp
    $dateOffers = date("d.m.Y", $timestamp);
    $timestamp2 = strtotime($validityOffer);
    $validityOffer = date("d.m.Y", $timestamp2);
    $nominalValue = number_format($bean->nominal_value, 2, '.', ' ');
    $price = $bean->price_c;
    $prefixNumber = $bean->prefix_number_c;
    $serviceAmountMonth = number_format($bean->service_amount_month_c, 2, '.', ' ');
    $delivery = $bean->delivery;
    $description = nl2br($bean->description);
    $quantity = 0;
    $employee = number_format(0, 2, '.', ' ');
    $periods = $bean->periodicity_c;
    $packaging = $bean->packaging;
 
    $query = "SELECT opp.quantity_c AS Quantity, opp.amount_employee_c AS Employee, bo.periodicity_c AS Periods
                FROM bzlnk_offers o
                LEFT JOIN bzlnk_offers_opportunities_c oop ON oop.bzlnk_offers_opportunitiesbzlnk_offers_idb = o.id AND oop.deleted=0
                LEFT JOIN opportunities op ON op.id = oop.bzlnk_offers_opportunitiesopportunities_ida AND op.deleted= 0
                LEFT JOIN opportunities_cstm opp ON opp.id_c = op.id
                LEFT JOIN bzlnk_offers_cstm bo ON bo.id_c = o.id
                WHERE o.id = '$id'";
 
    $data = $db->query($query);
 
    while ($row = $db->fetchByAssoc($data)) {
 
        if ($row['Quantity'] > 0) {
            $quantity = $row['Quantity'];
        }
        if ($row['Employee'] > 0) {
            $employee =  number_format($row['Employee'], 2, '.', ' ');
        }
        $periods = $app_list_strings['periodicity_list'][$periods];
        if (!is_numeric($serviceAmountMonth)) {
            $serviceAmountMonth = 0;
        }
        if (!is_numeric($delivery)) {
            $total = 0;
        }
        if (is_numeric($price)) {
            $price = $price . '%';
        }
    }
 
    $pdf = new TomboyPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
 
    $pdf->SetCreator("BIZLINK");
    $pdf->setauthor("BIZLINK");
    $pdf->SetFont("arial", '', 12);
    $pdf->SetAutoPageBreak(true, 28);
    $pdf->setPageOrientation("p");
 
 
    // add a Page1
    $pdf->AddPage();
    $image_page1 = dirname(__FILE__) . '/resources/Page1.PNG';
    $pdf->Image($image_page1, $pdf->GetX() - 10, +17, 210, 190, '', '', '', false, 300, 'C');
 
 
    $page1 = ' <br/> 
        <table width="508" border="1" "border-color: rgb(254, 154, 0);" cellspacing="0">
        <tr style="background-color:#ff9900;">
            <td>
                Фирма: ' . $account . '
            </td>
            <td>
                Съставил: ' . $assigned . '
            </td>
        </tr> 
        <tr>
            <td>
            На вниманието на:  ' . $contact_salutation . ' ' . $contacts . ' 
            </td>
            <td>
            Телефон: ' . $phone_work . '
            </td>
        </tr>
        <tr>
            <td>
            Позиция: 
            </td>
            <td>
            e-mail: ' . $email . '
            </td>
        </tr>
        <tr>
            <td>
            Изх. № ' . $prefixNumber . ' <br/>
            Дата: ' . $dateOffers . '
            </td>
            <td>
                Валидност на офертата ' . $validityOffer . '
            </td>
        </tr>
    </table>';
    $pdf->SetY(220);
    $pdf->writeHTML($page1);
 
    //Page2
    $pdf->AddPage();
    $image_page2 = dirname(__FILE__) . '/resources/Page2.PNG';
    $pdf->Image($image_page2, $pdf->GetX() - 10, +17, 210, 232, '', '', '', false, 300, 'C');
 
    //Page3
    $pdf->AddPage();
    $image_page3 = dirname(__FILE__) . '/resources/Page3.PNG';
    $pdf->Image($image_page3, $pdf->GetX() - 10, +17, 210, 235, '', '', '', false, 300, 'C');
 
    //Page4
    $pdf->AddPage();
    $image_page4 = dirname(__FILE__) . '/resources/Page4.PNG';
    $pdf->Image($image_page4, $pdf->GetX() - 10, +17, 210, 235, '', '', '', false, 300, 'C');
 
    //Page5
    $pdf->AddPage();
    $image_page5 = dirname(__FILE__) . '/resources/Page5.PNG';
    $pdf->Image($image_page5, $pdf->GetX() - 10, +17, 210, 232, '', '', '', false, 300, 'C');
 
    //Page6
    $pdf->AddPage();
    $image_page6 = dirname(__FILE__) . '/resources/Page6.PNG';
    $pdf->Image($image_page6, $pdf->GetX() - 10, +17, 210, 25, '', '', '', false, 300, 'C');
 
    $white_spaces = str_repeat('&nbsp;', 17);
    $white_spaces2 = str_repeat('&nbsp;', 10);
    $white_spaces3 = str_repeat('&nbsp;', 60);
 
    $image_page7 = '<p> </p>
        <p> </p>
        <p> </p>
        <p style="font-size: 30px;">Уважаеми ' . $contact_salutation . ' ' . $contacts . ' ,</p>
        <p style="font-size: 30px;">Имам удоволствието да Ви представя оферта за нашите услуги в издаването на ваучери за храна „.......” по Наредба №7, чл. 209 от ЗКПО</p>
        <p style="font-size: 30px;"><br/> <span style="color: #ff9900">' . $white_spaces . '•</span> Брой служители във фирмата: <span style="color: #ff9900">' . $quantity . '</span> души </p>
        <p style="font-size: 30px;"><br/> <span style="color: #ff9900">' . $white_spaces . '•</span> Номинална стойност на ваучерите: <span style="color: #ff9900">' . $employee . ' лв.</span> на служител на месец </p>
        <b>' . $white_spaces . 'Финансова стойност на поръчката:</b><br>
        <table style="width:100%" border="1" style="border-color: rgb(254, 154, 0); text-align:center; text-valign:center;" cellspacing="0" >
            <tr style="background-color:#ff9900; color: #ffffff">
                <th>Брой служители</th>
                <th>Ваучер на служител на месец</th>
                <th>Периодичност на поръчката</th>
                <th>Икономии</th>
                <th>Обща стойност ваучери</th>
                <th>Възнаграждение за производство/%</th>
                <th>Стойност на услугата на месец (без ДДС)</th>
            </tr>
            <tr>
                <td>' . $quantity . ' </td>
                <td>' . $employee . ' </td>
                <td>' . $periods . ' </td>
                <td>' . $packaging . ' </td>
                <td>' . $nominalValue . '</td>
                <td>' . $price . ' </td>
                <td>' . $serviceAmountMonth . ' </td>
            </tr>
        </table>
        <table style="width:100%" border="1" style="border-color: rgb(254, 154, 0);">
        <tr style="background-color:#ff9900;">
            <th> </th>
        </tr>
        <tr>
            <td> <b>Цена за доставка: ' . $delivery . '</b></td>
        </tr>
        </table>
        <span style="font-size: 22px;">**Възможност за директна доставка до Вашия офис, съгласно таблицата с тарифи за доставки.</span>
        <br/>
        <br/>
 
        <table style="width:808" border="0" style="font-size: 28px;" >
        <tr >
            <th style="width:20%"><span style="color: #ff9900; font-size: 35px;">' . $white_spaces2 . '•</span></th>
            <th style="width:400">Регулярно месечно обслужване </th>
        </tr>
        <tr>
            <td style="width:20%"><span style="color: #ff9900; font-size: 35px;">' . $white_spaces2 . '•</span></td>
            <td style="width:400">Отпечатване името на фирмата</td>
        </tr>
        <tr>
            <td style="width:20%"><span style="color: #ff9900; font-size: 35px;">' . $white_spaces2 . '•</span></td>
            <td style="width:400">Отпечатване на ваучерите в съответствие с разпоредбите на действащото българско законодателство</td>
        </tr>
        <tr>
            <td style="width:20%"><span style="color: #ff9900; font-size: 35px;">' . $white_spaces2 . '•</span></td>
            <td style="width:400">Предоставяне на ваучерите в желания от Вас формат <b> книжка </b>, листове А4 или на бройка, <b>с номинал на купюрите по Ваш избор</b></td>
        </tr>
        <tr>
            <td style="width:20%"></td>
            <td style="width:400"></td>
        </tr>
        <tr>
            <td style="width:20%"> </td>
            <td style="width:400; font-size: 32px"><b> Допълнителни услуги:</b> </td>
        </tr>
        <tr>
            <td style="width:100%"> ' . $description . '</td>
            <td></td>
        </tr>
        </table>
 
        <br/>
        <br/>
 
 
        <p>Оставам изцяло на Ваше разположение, като вярвам в нашето успешно бъдещо сътрудничество.</p>
        <br/>
        <table>
        <tr>
        <td></td>
        <td> С уважение,</td>
        </tr>
        <tr>
        <td></td>
        <td>' . $white_spaces . '.............................</td>
        </tr>
        </table>
        ';
 
    $pdf->writeHTML($image_page7);
 
    $pdf->Output("'$account'-Naredba7.pdf", "D");
}
 
class TomboyPDF extends TCPDF
{
 
    function Header()
    {
 
        $logo_image_header =  dirname(__FILE__) . '/resources/Up-Header.png';
        $this->Image($logo_image_header, $this->GetX() + 105, 5, 50, 0, '', '', 'L', false, 300, 'C');
    }
 
    function Footer()
    {
 
        $logo_image_footer = dirname(__FILE__) . '/resources/UP-Footer.png';
        $this->Image($logo_image_footer, $this->GetX() + 150, 260, 25, 0, '', '', '', false, 100, 'C');
 
        $white_spaces = str_repeat('&nbsp;', 2);
 
        $html_footer = $white_spaces . '<table width="500" border="0" cellspacing="0" style="font-size: 24px; ">
         <tr>
            <td></td>
             <td align="left" valign="left">
             <p>
              <br/>
              <b Style="color: #ff9900; font-size: 29px;">.......   БЪЛГАРИЯ   ООД</b>
              <br/>
              София 1000, Бул. „.........” No11 ет. 7 и 8
              <br/>
              Тел. 02 /... 00 00 ׀факс 02 /... 58 73
              <br/>
              E-mail : <a href="info@....bg"><span style="color: #0000EE;"><u>info@........bg</u></span></a>
              <br/>
              Website: <a href="https://up........bg/"><span style="color: #0000EE;"><u>www.up........bg</u></span></a>
             </p>
           </td>
           </tr>
         </table>';
 
        $this->SetY(260);
        $this->writeHTML($html_footer);
    }
}