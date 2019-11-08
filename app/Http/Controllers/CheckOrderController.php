<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\TypeHelper;

class CheckOrderController extends Controller
{
    public function checkOrder(Request $request)
    {
        $inputValues = $request->custRef;
        $type = $request->refType;

        switch ($type) {
            case 'nCust':
                return $this->orderByNoCustomer($inputValues);
                // break;
            case 'nAcc': // nAcc = nomor pelanggan
                return $this->orderByNoPelanggan($inputValues);
                // break;
            case 'nPel':
                return $this->orderByNoPelayanan($inputValues);
                // break;
            default:
                return view('index');           
                break;
        }
    }

    public function orderByNoCustomer($inputValues)
    {
        // $inputValues = '88836454578';

        // working by manually inject value into sql query
        $sql = "with temp_custeventsource as (
            SELECT customer_ref, product_seq, event_source, event_source_txt
                 , LISTAGG(rating_tariff_id, ',')
                     WITHIN GROUP (ORDER BY customer_ref, product_seq, event_source, event_source_txt) 
                     AS rating_tariff
                 , LISTAGG(event_type_id, ',')
                     WITHIN GROUP (ORDER BY customer_ref, product_seq, event_source, event_source_txt) 
                     AS event_type
            FROM custeventsource
            where customer_ref = "."'".$inputValues."'"."
            and (end_dtm is null or trunc(end_dtm) > last_day(add_months(sysdate,-2)))
            GROUP BY customer_ref, product_seq, event_source, event_source_txt
            ), 
            temp_custproductattrdetails as (
            SELECT customer_ref, product_seq
                 , LISTAGG(attribute_value, ',')
                     WITHIN GROUP (ORDER BY customer_ref, product_seq, product_attribute_subid) 
                     AS attribute_value
                 , LISTAGG(product_attribute_subid, ',')
                     WITHIN GROUP (ORDER BY customer_ref, product_seq, product_attribute_subid) 
                     AS attribute_subid  
            FROM custproductattrdetails
            where customer_ref = "."'".$inputValues."'"."
            and (end_dat is null or trunc(end_dat) > last_day(add_months(sysdate,-2)))
            GROUP BY customer_ref, product_seq
            ),
            temp_productaddress as (
            select a.customer_ref, a.product_seq, a.address_seq
            , b.address_1||','||b.address_2||','||b.address_3||','||b.address_4||','||b.address_5||','||b.zipcode||','||c.country_name as address
            from custproductaddress a, address b, country c where a.customer_ref = "."'".$inputValues."'"."
            and a.customer_ref = b.customer_ref and a.address_seq = b.address_seq
            and b.country_id = c.country_id
            ),
            temp_custproductstatus as
            (
            SELECT distinct customer_ref, product_seq
            , max(effective_dtm) over (partition by customer_ref, product_seq)  as effective_dtm
            FROM custproductstatus where customer_ref = "."'".$inputValues."'"."
            order by 1,2
            )
            select a.customer_ref, c.account_num, to_number(a.product_seq) product_seq, a.parent_product_seq
            , a.product_id, i.product_name, i.product_desc
            , a.cust_order_num, a.supplier_order_num
            , b.product_status, b.status_reason_txt, to_char(b.effective_dtm,'yyyy-mm-dd hh24:mi:ss') status_dtm
            , c.product_label, f.cps_name, j.attribute_value, j.attribute_subid, k.address
            , d.tariff_id, d.start_dat as tariff_start, d.end_dat as tariff_end, d.product_quantity, d.additions_quantity, g.tariff_name, nvl(h.one_off_number,0)/10 nrc, nvl(h.recurring_number,0)/10 mrc
            , e.event_source, e.event_source_txt, e.rating_tariff, e.event_type
            from custproductstatus b, custproductdetails c, custproducttariffdetails d, temp_custproductattrdetails j
            , contractedpointofsupply f, tariff g, tariffelementband h, tariffelement z, product i
            , custhasproduct a
            left join temp_custeventsource e on a.customer_ref = e.customer_ref and a.product_seq = e.product_seq
            left join temp_productaddress k on a.customer_ref = k.customer_ref and a.product_seq = k.product_seq
            where a.customer_ref = "."'".$inputValues."'"."
            and a.customer_ref = b.customer_ref and a.product_seq = b.product_seq 
            and exists 
              (
                select 1 from temp_custproductstatus cps where b.effective_dtm = cps.effective_dtm and b.product_seq = cps.product_seq
                and cps.customer_ref = b.customer_ref
              )  
            and a.customer_ref = c.customer_ref and a.product_seq = c.product_seq
            and a.customer_ref = d.customer_ref and a.product_seq = d.product_seq
            and a.product_id = i.product_id
            and c.cps_id = f.cps_id
            and a.customer_ref = j.customer_ref and a.product_seq = j.product_seq
            and d.tariff_id = g.tariff_id
            and g.catalogue_change_id = (select catalogue_change_id from cataloguechange where catalogue_status = 3 and currency_code = 'IDR')
            and d.tariff_id = h.tariff_id and a.product_id = h.product_id
            and g.catalogue_change_id = h.catalogue_change_id
            and h.tariff_id = z.tariff_id and h.product_id = z.product_id and h.catalogue_change_id = z.catalogue_change_id
            and z.end_dat is null
            and (c.end_dat is null or c.end_dat > last_day(add_months(sysdate,-2)))
            and (d.end_dat is null or d.end_dat > last_day(add_months(sysdate,-2)))
            and z.start_dat = h.start_dat
            UNION
            select substr(a.account_num,0,11) as customer_ref, a.account_num, a.product_seq, null, a.otc_id, 'OTC', d.cticket
            , null, null, null, d.cticket, to_char(a.otc_dtm,'yyyy-mm-dd hh24:mi:ss') otc_dtm, a.otc_label, b.cps_name
            , null, null, null
            , a.otc_id, null, null, null, null, a.otc_txt
            , a.otc_mny/10
            , 0, null, null, null, null
            from account z, acchasonetimecharge a, contractedpointofsupply b, BULKIMPORTER.MAP_OTC@tibsprod2 c, ods_isiska.ca_ticket@tibsprod2 d 
            where z.customer_ref = "."'".$inputValues."'"."
            and a.account_num = z.account_num
            and a.cps_id = b.cps_id
            and a.otc_id = c.otc_id
            and c.id_ticket = d.id_ticket
            order by 2,3,status_dtm";
            
        $res = DB::select(DB::raw($sql));
        return view('index', compact('res', 'inputValues'));
    }

    public function orderByNoPelanggan($inputValues)
    {
        dd($inputValues);
    }

    public function orderByNoPelayanan($inputValues)
    {
        // $inputValues = '111607111959';
        $sql = "with temp_custeventsource as (
            SELECT a.customer_ref, a.product_seq, a.event_source, a.event_source_txt
                 , LISTAGG(a.rating_tariff_id, ',')
                     WITHIN GROUP (ORDER BY a.customer_ref, a.product_seq, a.event_source, a.event_source_txt) 
                     AS rating_tariff
                 , LISTAGG(a.event_type_id, ',')
                     WITHIN GROUP (ORDER BY a.customer_ref, a.product_seq, a.event_source, a.event_source_txt) 
                     AS event_type    
            FROM custeventsource a, custeventsource b
            where b.event_source = '111607111959'
            and b.event_type_id in (200,202)
            and a.customer_ref = b.customer_ref
            and (a.end_dtm is null or trunc(a.end_dtm) > last_day(add_months(sysdate,-2)))
            GROUP BY a.customer_ref, a.product_seq, a.event_source, a.event_source_txt
            ), 
            temp_custproductattrdetails as (
            SELECT a.customer_ref, a.product_seq
                 , LISTAGG(a.attribute_value, ',')
                     WITHIN GROUP (ORDER BY a.customer_ref, a.product_seq, a.product_attribute_subid) 
                     AS attribute_value
                 , LISTAGG(a.product_attribute_subid, ',')
                     WITHIN GROUP (ORDER BY a.customer_ref, a.product_seq, a.product_attribute_subid) 
                     AS attribute_subid  
            FROM custproductattrdetails a, custeventsource b
            where b.event_source = '111607111959'
            and b.event_type_id in (200,202)
            and a.customer_ref = b.customer_ref
            and (a.end_dat is null or trunc(a.end_dat) > last_day(add_months(sysdate,-2)))
            GROUP BY a.customer_ref, a.product_seq
            ),
            temp_productaddress as (
            select a.customer_ref, a.product_seq, a.address_seq
            , b.address_1||','||b.address_2||','||b.address_3||','||b.address_4||','||b.address_5||','||b.zipcode||','||c.country_name as address
            from custproductaddress a, address b, country c, custeventsource d
            where d.event_source = '111607111959'
            and d.event_type_id in (200,202)
            and a.customer_ref = d.customer_ref
            and a.customer_ref = b.customer_ref and a.address_seq = b.address_seq
            and b.country_id = c.country_id
            ),
            temp_custproductstatus as
            (
            SELECT distinct a.customer_ref, a.product_seq
            , max(a.effective_dtm) over (partition by a.customer_ref, a.product_seq)  as effective_dtm
            FROM custproductstatus a, custeventsource b
            where b.event_source = '111607111959'
            and b.event_type_id in (200,202)
            and a.customer_ref = b.customer_ref
            order by 1,2
            )
            select a.customer_ref, c.account_num, to_number(a.product_seq) product_seq, a.parent_product_seq
            , a.product_id, i.product_name, i.product_desc
            , a.cust_order_num, a.supplier_order_num
            , b.product_status, b.status_reason_txt, to_char(b.effective_dtm,'yyyy-mm-dd hh24:mi:ss') status_dtm
            , c.product_label, f.cps_name, j.attribute_value, j.attribute_subid, k.address
            , d.tariff_id, d.start_dat as tariff_start, d.end_dat as tariff_end, d.product_quantity, d.additions_quantity, g.tariff_name, nvl(h.one_off_number,0)/10 nrc, nvl(h.recurring_number,0)/10 mrc
            , e.event_source, e.event_source_txt, e.rating_tariff, e.event_type
            from custproductstatus b, custproductdetails c, custproducttariffdetails d, temp_custproductattrdetails j
            , contractedpointofsupply f, tariff g, tariffelementband h, tariffelement z, product i, custeventsource ces
            , custhasproduct a
            left join temp_custeventsource e on a.customer_ref = e.customer_ref and a.product_seq = e.product_seq
            left join temp_productaddress k on a.customer_ref = k.customer_ref and a.product_seq = k.product_seq
            where ces.event_source = '111607111959'
            and ces.event_type_id in (200,202)
            and a.customer_ref = ces.customer_ref
            and a.customer_ref = b.customer_ref and a.product_seq = b.product_seq 
            and exists 
              (
                select 1 from temp_custproductstatus cps where b.effective_dtm = cps.effective_dtm and b.product_seq = cps.product_seq
                and cps.customer_ref = b.customer_ref
              )  
            and a.customer_ref = c.customer_ref and a.product_seq = c.product_seq
            and a.customer_ref = d.customer_ref and a.product_seq = d.product_seq
            and a.product_id = i.product_id
            and c.cps_id = f.cps_id
            and a.customer_ref = j.customer_ref and a.product_seq = j.product_seq
            and d.tariff_id = g.tariff_id
            and g.catalogue_change_id = (select catalogue_change_id from cataloguechange where catalogue_status = 3 and currency_code = 'IDR')
            and d.tariff_id = h.tariff_id and a.product_id = h.product_id
            and g.catalogue_change_id = h.catalogue_change_id
            and h.tariff_id = z.tariff_id and h.product_id = z.product_id and h.catalogue_change_id = z.catalogue_change_id
            and z.end_dat is null
            and (c.end_dat is null or c.end_dat > last_day(add_months(sysdate,-2)))
            and (d.end_dat is null or d.end_dat > last_day(add_months(sysdate,-2)))
            and z.start_dat = h.start_dat
            UNION
            select substr(a.account_num,0,11) as customer_ref, a.account_num, a.product_seq, null, a.otc_id, 'OTC', d.cticket
            , null, null, null, d.cticket, to_char(a.otc_dtm,'yyyy-mm-dd hh24:mi:ss') otc_dtm, a.otc_label, b.cps_name
            , null, null, null
            , a.otc_id, null, null, null, null, a.otc_txt
            , a.otc_mny/10
            , 0, null, null, null, null
            from account z, acchasonetimecharge a, contractedpointofsupply b, BULKIMPORTER.MAP_OTC@tibsprod2 c, ods_isiska.ca_ticket@tibsprod2 d
            , custeventsource ces
            where ces.event_source = '111607111959'
            and ces.event_type_id in (200,202)
            and z.customer_ref = ces.customer_ref
            and a.account_num = z.account_num
            and a.cps_id = b.cps_id
            and a.otc_id = c.otc_id
            and c.id_ticket = d.id_ticket
            order by 2,3,status_dtm";
        
        $res = DB::select(DB::raw($sql));
        return view('index', compact('res', 'inputValues'));
    }   
}