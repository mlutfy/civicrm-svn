<?xml version="1.0" encoding="utf-8"?>
<ARBCreateSubscriptionRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
  <merchantAuthentication>
    <name>{$apiLogin}</name>
    <transactionKey>{$paymentKey}</transactionKey>
  </merchantAuthentication>
  <refId>{$refId}</refId>
  <subscription>
    <name>{$name}</name>
    <paymentSchedule>
      <interval>
        <length>{$intervalLength}</length>
        <unit>{$intervalUnit}</unit>
      </interval>
      <startDate>{$startDate}</startDate>
      <totalOccurrences>{$totalOccurrences}</totalOccurrences>
    </paymentSchedule>
    <amount>{$amount}</amount>
    <payment>
      <creditCard>
        <cardNumber>{$cardNumber}</cardNumber>
        <expirationDate>{$expirationDate}</expirationDate>
      </creditCard>
    </payment>
    <customer>
      <email>{$email}</email>
    </customer>
    <billTo>
      <firstName>{$billingFirstName}</firstName>
      <lastName>{$billingLastName}</lastName>
      <address>{$billingAddress}</address>
      <city>{$billingCity}</city>
      <state>{$billingState}</state>
      <zip>{$billingZip}</zip>
      <country>{$billingCountry}</country>
    </billTo>
  </subscription>
</ARBCreateSubscriptionRequest>
