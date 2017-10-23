# rlts_certification
RLTS_Certification is a module that updates the customer's statuses and send email reminders using Magento's Cron jobs.
We created custom customer attribute (id: certi_status) of drop down type with INACTIVE, CANDIDATE and CERTIFIED status of customers.

When a customer is created, its INACTIVE. When customer purchases the class, its status changes to CANDIDATE. When a CANDIDATE successfully completes the class and exam, its status changes to CERTIFIED. 
The CANDIDATE status will be INACTIVE after 6 months and CERTIFIED status will be INACTIVE after two years.   
In this module, we're sending email reminder to the CERTIFIED customers to renew his/her certification. Also on duration completion. this module will change the status of customer.