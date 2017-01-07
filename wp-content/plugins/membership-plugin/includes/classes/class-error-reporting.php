class GLPConfig 
{ 
// Setup database connection details 
const DB_HOST = 'localhost'; 
const DB_NAME = 'ctxphc_php_error_logging'; 
const DB_PORT = '3306'; 
const DB_USER = 'ctxphcco_admin'; 
const DB_PASSWORD = 'P@rr0theads'; 
 
// Setup configuration for email reports 
const EMAIL_TO = 'support@ctxphc.com'; 
const EMAIL_FROM = 'support@ctxphc.com'; 
const SITE_NAME = 'Central Texas Parrothead Club'; 
 
// Setup configuration of throttling mechanisms 
const SEND_DIGESTS = true; 
/* whether to queue error report emails. Requires the database setup: see footer of this file for details */

const DIGEST_INTERVAL = '30'; // How often to send digests, in minutes 
const DUPLICATE_INTERVAL = '120'; // How long to remember duplicates, in minutes 
const PREVENT_DUPLICATES = true; // whether to try to filter duplicate error reports 
} 
 
// Hook our stub function to the PHP shutdown event 
register_shutdown_function('glpShutdown'); 
 
// Our stub function will instantiate the GLPErrorReport class if there has been an error 
function glpShutdown() { 
if ( $error = error_get_last()) { 
$GLPErrorReport = new GLPErrorReport($error); 
} else { 
// No errors, just quit 
} 
} 
 
class GLPDB 
{ 
public $db; 
private static $_instance; 
private function __construct() 
{ 
// building data source name from config 
$dsn = 'mysql:host=' . GLPConfig::DB_HOST . ';dbname=' . GLPConfig::DB_NAME .';port=' . GLPConfig::DB_PORT . ';connect_timeout=15'; 
try { 
$this->db = new PDO($dsn, GLPConfig::DB_USER, GLPConfig::DB_PASSWORD); 
} catch (Exception $e) { 
user_error('The database is not configured correctly'); 
} 
} 
 
// cannot clone singleton 
protected function __clone() 
{ 
 
} 
 
// singleton class returns itself 
public static function getInstance() 
{ 
if( self::$_instance === NULL ) { 
self::$_instance = new self(); 
} 
return self::$_instance; 
} 
} 
 
class GLPErrorReport 
{ 
 
function __construct($error) 
{ 
$this->errorType = array ( 
E_ERROR => 'E_ERROR', 
E_WARNING => 'E_WARNING', 
E_PARSE => 'E_PARSE', 
E_NOTICE => 'E_NOTICE', 
E_CORE_ERROR => 'E_CORE_ERROR', 
E_CORE_WARNING => 'E_CORE_WARNING', 
E_COMPILE_ERROR => 'E_COMPILE_ERROR', 
E_COMPILE_WARNING => 'E_COMPILE_WARNING', 
E_USER_ERROR => 'E_USER_ERROR', 
E_USER_WARNING => 'E_USER_WARNING', 
E_USER_NOTICE => 'E_USER_NOTICE', 
E_STRICT => 'E_STRICT', 
E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR' 
); 
$this->error = $error; 
$messageShouldBeQueued = true; 
$this->setupDB(); 
// check whether we are filtering duplicate errors 
if ($this->shouldFilterDuplicates()) { 
// setup the db here: we might not need it unless that last if evaluates to true 
// check whether this is a duplicate error of an already-queued error 
if ($this->isMessageADuplicate()) { 
$messageShouldBeQueued = false; 
} 
} 
if ($messageShouldBeQueued) { 
$this->createMessage(); 
$this->queueMessage(); 
} 
// shutdown routine: go get any messages and send them 
$this->checkQueues(); 
$this->purgeQueue(); 
} 
 
function checkQueues() { 
$query = "SELECT * FROM error_report WHERE 
`dt_generated` > DATE_SUB(NOW(), INTERVAL " . GLPConfig::DIGEST_INTERVAL . " MINUTE ) AND `sent` = 'n' ORDER BY dt_generated DESC;"; 
$stmt = GLPDB::getInstance()->db->prepare($query); 
if ($stmt->execute()) { 
$o = $stmt->fetchAll(PDO::FETCH_OBJ); 
//var_dump($o); 
if (count($o) > 0) { 
$date = new DateTime(); 
if ($this->shouldProduceDigest()) { 
$digestContent = ''; 
foreach ($o as $oneRow) { 
$digestContent.= $oneRow->error_detail; 
$this->markMessageSent($oneRow->id); 
} 
$subject = 'Notification from site: ' . GLPConfig::SITE_NAME .' at ' . $date->format('Y-m-d H:i:s'); 
$this->send($subject, $digestContent); 
} else { 
foreach ($o as $oneRow) { 
$subject = 'Notification from site: ' . GLPConfig::SITE_NAME .' at ' . $date->format('Y-m-d H:i:s'); 
$this->send($subject, $oneRow->error_detail); 
$this->markMessageSent($oneRow->id); 
} 
 
} 
} 
} 
} 
 
function purgeQueue() { 
$query = "DELETE FROM error_report WHERE sent = 'y' AND dt_generated < DATE_SUB(NOW(), INTERVAL " . GLPConfig::DUPLICATE_INTERVAL. " MINUTE );"; 
$stmt = GLPDB::getInstance()->db->prepare($query); 
$stmt->execute(); 
} 
 
function send($subject, $emailcontents) { 
$message = '<html><head> 
<title>' . $subject . '</title> 
<style> 
.report .errorbasics { background: #FADD3C; padding: 20px; font-weight: 900; 
font-size: 14px; border: 1px solid #D6BE36} 
.E_ERROR .errorbasics, .E_CORE_ERROR .errorbasics, .E_COMPILE_ERROR 
.errorbasics, .E_USER_ERROR .errorbasics { background: #DB0000; color: #fff; 
border: 1px solid #9b0000} 
</style> 
</head> 
<body>'; 
$message .= $emailcontents; 
$message .='</body></html>'; 
$headers = 'MIME-Version: 1.0' . "\r\n"; 
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n"; 
$headers .= 'From: ' . GLPConfig::EMAIL_FROM . "\r\n"; 
mail(GLPConfig::EMAIL_TO, $subject, $message, $headers); 
} 
 
function markMessageSent($id) { 
$query = "UPDATE error_report SET sent = 'y' WHERE id = :id"; 
$stmt = GLPDB::getInstance()->db->prepare($query); 
$stmt->bindParam(':id', $id, PDO::PARAM_INT); 
$stmt->execute(); 
} 
 
function queueMessage() { 
$dtString = 'now()'; 
$query = "INSERT INTO error_report (message_d, file_name, line_no, error_detail, dt_generated) VALUES ( :message, :file, :lineNo, :detail, NOW())"; 
$stmt = GLPDB::getInstance()->db->prepare($query); 
$stmt->bindParam(':message', $this->error["message"], PDO::PARAM_STR); 
$stmt->bindParam(':file', $this->error["file"], PDO::PARAM_STR); 
$stmt->bindParam(':lineNo', $this->error["line"], PDO::PARAM_INT); 
$stmt->bindParam(':detail', $this->message, PDO::PARAM_STR); 
$stmt->execute(); 
 
} 
 
function createMessage() { 
$backtrace = debug_backtrace(); 
$message = '<div class="report ' . $this->errorType[$this->error["type"]].'">'; 
$message .= '<p class="errorbasics">' . $this->error["message"] . '<br/>'; 
$message .= $this->error["file"] . ', line: ' . $this->error["line"] . '<br/>'; 
$message .= 'URL: ' . $_SERVER["SERVER_NAME"] . $_SERVER['REQUEST_URI'] . 
'</p>'; 
switch($this->error['type']){ 
case E_ERROR: 
case E_CORE_ERROR: 
case E_COMPILE_ERROR: 
case E_USER_ERROR: 
$message .= '<p>Error-specific Details follow</p>'; 
// Any error-specific content here 
break; 
default: 
$message .= '<p>Notice-specific Details follow</p>'; 
// Any notice-specific content here 
break; 
} 
ob_start(); 
echo "<h2>backtrace:</h2><div style='text-align: left; font-family: 
monospace;'>\n<pre>"; 
array_shift($backtrace); 
// array_shift($backtrace); 
var_dump($backtrace); 
echo '</pre><h2>session:</h2>'; 
if (isset($_SESSION)) { 
echo '<pre>'; 
var_dump($_SESSION); 
echo '</pre>'; 
} 
echo '<h2>server:</h2><pre>'; 
var_dump($_SERVER); 
echo '</pre><h2>get:</h2><pre>'; 
var_dump($_GET); 
echo '</pre><h2>post:</h2><pre>'; 
var_dump($_POST); 
echo '</pre></div>'; 
$message .= ob_get_contents(); 
ob_end_clean(); 
$this->message = $message; 
} 
 
function isMessageADuplicate() { 
$query = "SELECT * FROM error_report WHERE 
file_name = :file AND 
line_no = :lineNo AND 
message_d = :message AND 
dt_generated > DATE_SUB(NOW(), INTERVAL " . GLPConfig::DU- 
PLICATE_INTERVAL. " MINUTE );"; 
try { 
$msgVal = ''; 
$stmt = GLPDB::getInstance()->db->prepare($query); 
$stmt->bindParam(':file', $this->error["file"], PDO::PARAM_STR); 
$stmt->bindParam(':lineNo', $this->error["line"], PDO::PARAM_INT); 
$stmt->bindParam(':message', $this->error["message"], PDO::PARAM_STR); 
if ($stmt->execute()) { 
$o = $stmt->fetch(PDO::FETCH_OBJ); 
if ($o) { 
return true; 
} 
} 
}catch (Exception $e) { 
var_dump($e); 
} 
return false; 
} 
 
function setupDB() { 
GLPDB::getInstance(); 
$query = "DESCRIBE error_report;"; 
} 
 
function confirm_query($result_set) { 
if (!$result_set){ 
//die("Database query failed: " . mysql_error()); 
} else { 
return true; 
} 
} 
 
function shouldProduceDigest() { 
return GLPConfig::SEND_DIGESTS; 
} 
 
function shouldFilterDuplicates() { 
return GLPConfig::PREVENT_DUPLICATES; 
} 
} 