<?php 

	$carpetaxml = "xml/";
	$carpetacdr = "cdr/";

	$emisor = array(
				"tipo_documento" => 6,
				"ruc"	=> "20607599727",
				"razon_social" => "INSTITUTO INTERNACIONAL DE SOFTWARE S.A.C.",
				"nombre_comercial" => "ACADEMIA DE SOFTWARE",
				"departamento" => "LAMBAYEQUE",
				"provincia" => "CHICLAYO",
				"distrito" => "CHICLAYO",
				"direccion" => "CALLE OCHO DE OCTUBRE 123",
				"ubigeo" => "140101",
				"usuario_sol" => "MODDATOS",
				"clave_sol" => "MODDATOS"
				);

	$cliente = array(
				"tipo_documento" => "6",
				"ruc" => "20605145648",
				"razon_social" => "AGROINVERSIONES Y SERVICIOS AJINOR S.R.L. - AGROSERVIS AJINOR S.R.L.",
				"direccion" => "MZA. C LOTE. 46 URB. SAN ISIDRO LA LIBERTAD - TRUJILLO - TRUJILLO"
				);


	$cabecera = array(
				"tipo_comprobante" => "09",
				"serie"				=> "T001",
				"correlativo"		=> 1234,
				"fecha_emision"		=>"2021-08-24",
				"hora_emision"		=>"19:43:00",
				"fecha_envio" 		=>"2021-08-24"
				);

	$items =array();
	
	$items[] = array(
				"item"   => 1,
				"id"	 => 123,
				"cantidad"   => 500,
				"unidad"   => "NIU",
				"nombre" => "MOCHILA"
				);	

	$nombrexml = $emisor['ruc']."-".$cabecera['tipo_comprobante']."-".$cabecera['serie']."-".$cabecera['correlativo'];

	$doc = new DOMDocument();
	$doc->formatOutput = FALSE;
	$doc->preserveWhiteSpace = TRUE;
	$doc->encoding = 'utf-8';
	$xml = '<?xml version="1.0" encoding="utf-8"?>
<DespatchAdvice xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" xmlns:qdt="urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2" xmlns:ccts="urn:un:unece:uncefact:documentation:2" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:udt="urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2" xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:sac="urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1" xmlns="urn:oasis:names:specification:ubl:schema:xsd:DespatchAdvice-2">
    <ext:UBLExtensions>
        <ext:UBLExtension>
            <ext:ExtensionContent/>
        </ext:UBLExtension>
    </ext:UBLExtensions>
    <cbc:UBLVersionID>2.1</cbc:UBLVersionID>
    <cbc:CustomizationID>1.0</cbc:CustomizationID>
    <cbc:ID>'.$cabecera['serie'].'-'.$cabecera['correlativo'].'</cbc:ID>
    <cbc:IssueDate>'.$cabecera['fecha_emision'].'</cbc:IssueDate>
    <cbc:IssueTime>'.$cabecera['hora_emision'].'</cbc:IssueTime>
    <cbc:DespatchAdviceTypeCode>'.$cabecera['tipo_comprobante'].'</cbc:DespatchAdviceTypeCode>
    <cbc:Note>--</cbc:Note>
	<cac:Signature>
		<cbc:ID>'.$cabecera['serie'].'-'.$cabecera['correlativo'].'</cbc:ID>
		<cac:SignatoryParty>
			<cac:PartyIdentification>
				<cbc:ID>'.$emisor['ruc'].'</cbc:ID>
			</cac:PartyIdentification>
			<cac:PartyName>
				<cbc:Name><![CDATA['.$emisor['razon_social'].']]></cbc:Name>
			</cac:PartyName>
		</cac:SignatoryParty>
		<cac:DigitalSignatureAttachment>
			<cac:ExternalReference>
				<cbc:URI>#SignatureSP</cbc:URI>
			</cac:ExternalReference>
		</cac:DigitalSignatureAttachment>
	</cac:Signature>    
    <cac:DespatchSupplierParty>
            <cbc:CustomerAssignedAccountID schemeID="6">'.$emisor['ruc'].'</cbc:CustomerAssignedAccountID>
            <cac:Party>
                <cac:PartyLegalEntity>
                    <cbc:RegistrationName><![CDATA['.$emisor['razon_social'].']]></cbc:RegistrationName>
                </cac:PartyLegalEntity>
            </cac:Party>
        </cac:DespatchSupplierParty>    
    <cac:DeliveryCustomerParty>
    <cbc:CustomerAssignedAccountID schemeID="6">'.$cliente['ruc'].'</cbc:CustomerAssignedAccountID>
        <cac:Party>
            <cac:PartyLegalEntity>
                <cbc:RegistrationName><![CDATA['.$cliente['razon_social'].']]></cbc:RegistrationName>
            </cac:PartyLegalEntity>
        </cac:Party>
    </cac:DeliveryCustomerParty>    
    <cac:Shipment>
            <cbc:ID>1</cbc:ID>
            <cbc:HandlingCode>01</cbc:HandlingCode>
            <cbc:Information>VENTA</cbc:Information>
            <cbc:GrossWeightMeasure unitCode="KGM">5500.00</cbc:GrossWeightMeasure>
            <cac:ShipmentStage>
                <cbc:TransportModeCode>02</cbc:TransportModeCode>
                <cac:TransitPeriod>
                    <cbc:StartDate>2021-09-01</cbc:StartDate>
                </cac:TransitPeriod>
                <cac:TransportMeans>
    	            <cac:RoadTransport>
    	               <cbc:LicensePlateID>M4S-945</cbc:LicensePlateID>
    	            </cac:RoadTransport>
		        </cac:TransportMeans>
		         <cac:DriverPerson>
		            <cbc:ID schemeID="1">47163065</cbc:ID>
		         </cac:DriverPerson>		        
            </cac:ShipmentStage>            
            <cac:Delivery>
                <cac:DeliveryAddress>
                    <cbc:ID>140306</cbc:ID>
                    <cbc:StreetName><![CDATA[LOCAL ESCOLAR Nª11572 MOCHICA DEL C.P 25 DE FEBRERO MORROPE-LAMBAYEQUE-LAMBAYEQUE]]></cbc:StreetName>
                </cac:DeliveryAddress>
            </cac:Delivery>
            
            <cac:OriginAddress>
                        <cbc:ID>140306</cbc:ID>
                        <cbc:StreetName><![CDATA[CAR.PAMERICANA NORTE KM. 807 OTR. PANAMERICANA NORTE LAMBAYEQUE - LAMBAYEQUE - MORROPE]]></cbc:StreetName>
            </cac:OriginAddress>
        </cac:Shipment>';
        
        foreach($items as $k => $v){
        $xml.='<cac:DespatchLine>
            <cbc:ID>'.$v['item'].'</cbc:ID>
            <cbc:DeliveredQuantity unitCode="'.$v['unidad'].'">'.$v['cantidad'].'</cbc:DeliveredQuantity>
            <cac:OrderLineReference>
                <cbc:LineID>'.$v['item'].'</cbc:LineID>
            </cac:OrderLineReference>    
            <cac:Item>
                <cbc:Name><![CDATA['.$v['nombre'].']]></cbc:Name>
                <cac:SellersItemIdentification>
                    <cbc:ID>'.$v['id'].'</cbc:ID>
                </cac:SellersItemIdentification>
            </cac:Item>
        </cac:DespatchLine>';
        }
 	$xml.='</DespatchAdvice>
';

	$doc->loadXML($xml);
	$doc->save($carpetaxml.$nombrexml.'.XML');


require_once("signature.php");
$objSignature = new Signature();

$flg_firma = "0";
$ruta = $carpetaxml.$nombrexml.'.XML';

$ruta_firma = "certificado_prueba.pfx";
$pass_firma = "institutoisi";

$resp = $objSignature->signature_xml($flg_firma, $ruta, $ruta_firma, $pass_firma);

print_r($resp);

//PASO 03
$zip = new ZipArchive();
$nombrezip = $nombrexml.".ZIP";
$rutazip = $carpetaxml.$nombrexml.".ZIP";

if($zip->open($rutazip,ZIPARCHIVE::CREATE)===true){
	$zip->addFile($carpetaxml.$nombrexml.'.XML', $nombrexml.'.XML');
	$zip->close();
}

//PASO 04
//PREPARAR EL ENVÍO DEL XML
$contenido_del_zip = base64_encode(file_get_contents($rutazip));
$xml_envio ='<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" 
        xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.sunat.gob.pe" 
        xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
     <soapenv:Header>
            <wsse:Security>
                <wsse:UsernameToken>
                    <wsse:Username>'.$emisor['ruc'].$emisor['usuario_sol'].'</wsse:Username>
	<wsse:Password>'.$emisor['clave_sol'].'</wsse:Password>
                </wsse:UsernameToken>
           </wsse:Security>
 </soapenv:Header>
 <soapenv:Body>
	<ser:sendBill>
		<fileName>'.$nombrezip.'</fileName>
		<contentFile>'.$contenido_del_zip.'</contentFile>
	</ser:sendBill>
 </soapenv:Body>
</soapenv:Envelope>';

//PASO 05
//ENVÍO DEL CPE A WS DE SUNAT

$ws = "https://e-beta.sunat.gob.pe/ol-ti-itemision-guia-gem-beta/billService";
$header = array(
			"Content-type: text/xml; charset=\"utf-8\"",
			"Accept: text/xml",
			"Cache-Control: no-cache",
			"Pragma: no-cache",
			"SOAPAction: ",
			"Content-lenght: ".strlen($xml_envio)
		);

$ch = curl_init();
curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,1);
curl_setopt($ch,CURLOPT_URL,$ws);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch,CURLOPT_HTTPAUTH,CURLAUTH_ANY);
curl_setopt($ch,CURLOPT_TIMEOUT,30);
curl_setopt($ch,CURLOPT_POST,true);
curl_setopt($ch,CURLOPT_POSTFIELDS,$xml_envio);
curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__)."/cacert.pem");
$response = curl_exec($ch);


//PASO 06 
// OBTENEMOS RESPUESTA (CDR)
$httpcode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
if($httpcode == 200){
	$doc = new DOMDocument();
	$doc->loadXML($response);
		if(isset($doc->getElementsByTagName('applicationResponse')->item(0)->nodeValue)){
			$cdr = $doc->getElementsByTagName('applicationResponse')->item(0)->nodeValue;
			$cdr = base64_decode($cdr);			
			file_put_contents($carpetacdr."R-".$nombrezip, $cdr);
			$zip = new ZipArchive;
			if($zip->open($carpetacdr."R-".$nombrezip)===true){
				$zip->extractTo($carpetacdr.'R-'.$nombrexml);
				$zip->close();
			}
			echo "GUIA ENVIADA CORRECTAMENTE";
		}else{		
			$codigo = $doc->getElementsByTagName("faultcode")->item(0)->nodeValue;
			$mensaje = $doc->getElementsByTagName("faultstring")->item(0)->nodeValue;
			echo "error ".$codigo.": ".$mensaje; 
		}
}else{
		echo curl_error($ch);
		echo "Problema de conexión";
}
curl_close($ch);

?>