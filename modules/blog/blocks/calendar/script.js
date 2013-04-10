function block_calendar() {
    
    block.call(this);  
    
    this.name = "calendar";

    this.stylableElements = {
	"calendar":".calendar",
	"calendar inner":".calendarInner",
	"a day":".day",
        "a link day":".day a",
	"a week":".week",
	"month title":".monthTitle",
	"week title":".weekTitle",
	"days which has posts":".day.hasposts",
	"days link which has post":".day.hasposts a",
	"days which are in this month":".day.thisMonth",
	"days which aren't in this month":".day.out",
	"calendar navigation":".calendarNav",
        "calendar navigation links":".calendarNav a",
	"link previous month":".prevMonth",
	"link next month":".nextMonth"
    }
    
}