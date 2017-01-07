SELECT a.first_name, a.last_name, a.email, a.phone, a.bday, a.hatch_date, a.tag_date, a.initiated_date, a.occupation, a.status_id, a.membership_id, a.wp_login,
  b.addr1, b.addr2, b.city, b.state, b.zip
FROM ctxphc_members a
  join ctxphc_member_addresses b
WHERE a.address_id = b.ID

UNION ALL

SELECT memb_fname AS first_name
  , memb_lname AS last_name
  , memb_email as email
  , memb_phone as phone
  , memb_bday_day
  , memb_bday_month
  , CONCAT_WS('/', memb_bday_month, memb_bday_day) AS birthday
  , memb_hatch_date AS hatch_date
  , memb_tag AS tag_date
  , memb_occup AS occupation
  , status_id AS status_id
  , memb_type AS membership_id
  , memb_user AS wp_username
  , memb_addr AS addr1
  , memb_addr2 AS addr2
  , memb_city AS city
  , memb_state AS state
  , memb_zip AS zip
from ctxphc_ctxphc_members;


