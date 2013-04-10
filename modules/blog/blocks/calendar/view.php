<?php
/**
 * Parsimony
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@parsimony.mobi so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.parsimony.mobi for more information.
 *
 * @authors Julien Gras et Benoît Lorillot
 * @copyright  Julien Gras et Benoît Lorillot
 * @version  Release: 1.0
 * @category  Parsimony
 * @package blog/blocks
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


$monthNames = Array('January', 'February', 'March', "April", 'May', 'June', 'July',
    'August', 'September', 'October', 'November', 'December');

if (!isset($_REQUEST['month']))
    $thisMonth = date('n');
else
    $thisMonth = $_REQUEST['month'];
if (!isset($_REQUEST['year']))
    $thisYear = date('Y');
else
    $thisYear = $_REQUEST['year'];

$prev_year = $thisYear;
$next_year = $thisYear;
$prev_month = $thisMonth - 1;
$next_month = $thisMonth + 1;

if ($prev_month <= 0) {
    $prev_month = 12;
    $prev_year = $thisYear - 1;
}
if ($next_month >= 13) {
    $next_month = 1;
    $next_year = $thisYear + 1;
}

$firstDayOfMonth = mktime(0, 0, 0, $thisMonth, 1, $thisYear);
$maxday = date('t', $firstDayOfMonth);

$daysOfThisMonth = array();
for ($i = 1; $i <= $maxday; $i++)
    $daysOfThisMonth[$i] = 0;

$query = \PDOconnection::getDB()->query('SELECT publicationGMT FROM '.PREFIX.'blog_post WHERE '.PREFIX.'publicationGMT BETWEEN \'' . gmdate('Y-m-d H:i:s', $firstDayOfMonth) . '\' AND \'' . gmdate('Y-m-d H:i:s', mktime(0, 0, 0, $thisMonth, $maxday, $thisYear)) . '\'');
if(is_object($query)){
    $recposts = $query->fetchAll(\PDO::FETCH_ASSOC);
    if (!empty($recposts)) {
	foreach ($recposts as $key => $post) {
	    $daysOfThisMonth[date('j', strtotime($post['publicationGMT']))]++;
	}
    }
?>
<div class="calendar">
    <div class="monthTitle"><?php echo t($monthNames[$thisMonth - 1]) . ' ' . $thisYear; ?></div>
    <div class="calendarInner">
        <div class="weekTitle">
            <div class="day">M</div>
            <div class="day">T</div>
            <div class="day">W</div>
            <div class="day">T</div>
            <div class="day">F</div>
            <div class="day">S</div>
            <div class="day">S</div>
            <div style="clear:both"></div>
        </div>
        <div class="week">
            <?php
            $totalDays = $maxday;
            $thismonth = getdate($firstDayOfMonth);
            $startday = $thismonth['wday'];
	    if($startday == 0) $startday = 7;
            if ($startday >= 1) {
                $totalDays = --$startday + $maxday;
                for ($i = 1 - $startday; $i <= 0; $i++)
                    echo '<div class="day out">' . date('d', mktime(0, 0, 0, $thisMonth, $i, $thisYear)) . '</div>';
            }
	    $nb = $startday;
            foreach ($daysOfThisMonth as $day => $nbPosts) {
                if ($nb == 7){
                    echo '<div style="clear:both"></div></div><div class="week">';
		    $nb = 0;
		}
                if ($nbPosts > 0)
                    echo '<div class="day thisMonth hasposts"><a href="'.BASE_PATH.$thisYear.'/'.sprintf('%02d',$thisMonth).'/'.sprintf('%02d',$day).'">' . $day . '</a></div>';
                else
                    echo '<div class="day thisMonth">' . $day . '</div>';
		$nb++;
            }
            $bonus = 42 - $totalDays;
            if ($bonus >= 7)
                $bonus = $bonus - 7;
            for ($i = 1; $i <= $bonus; $i++)
                echo '<div class="day out">' . $i . '</div>';
            ?>
        </div>
    </div>
    <div class="calendarNav">
        <a href="<?php echo $_SERVER['PHP_SELF'] . '?month=' . $prev_month . '&year=' . $prev_year; ?>" class="prevMonth" title="<?php echo t($monthNames[$prev_month - 1]) ?>">< <span><?php echo t($monthNames[$prev_month - 1]) ?></span></a>
        <?php if ($next_month <= date('n')): ?>
            <a href="<?php echo $_SERVER['PHP_SELF'] . '?month=' . $next_month . '&year=' . $next_year; ?>" class="nextMonth" title="<?php echo t($monthNames[$next_month - 1]) ?>"><span><?php echo t($monthNames[$next_month - 1]) ?></span> ></a>
        <?php endif; ?>
    </div>
</div>
<?php } ?>