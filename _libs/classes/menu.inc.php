<?php
	// Provides CLASSES that will be handling all security concerns
	// REQUIRES the menu.inc.php file

	//Outputs the menu for the website depending on the privileges needed

	//if SUPERADMIN, all checking of privileges is ignored
	//menu privileges contain the privileges of a user that is allowed to view the menu
	class MenuPrivileges{
		private $index = 0;
		private $privileges = Array(); //only privileges allowed

		//add privilege to list
		function AddPrivilege($privilege){
			$this->privileges[$this->index] = $privilege;
			$this->index++;
		}

		//check if the array contains the Contains one of the user privileges
		function Contains($UserPrivileges){
			foreach($this->privileges as $privilege){
				foreach($UserPrivileges as $userPrivilege){
					if($privilege == $userPrivilege->title){
						return true;
					}
				}
			}

			return false;
		}
	}

	//the item in the menu list
	class MenuItem{
		public $id;
		public $display;
		public $url;
		public $title;
		private $privilege;

		//if privilege is null, allowed for all
		function __construct($display, $url, $title="", $id="", $privilege=null){
			$this->display = $display;
			$this->url = $url;
			$this->title = $title;
			$this->privilege = $privilege;
			$this->id = $id;
		}

		//checks whether menu is allowed for user
		public function CheckPrivilege($UserPrivilege){
			if($this->privilege == null){
				return true;
			} else {
				return $this->privilege->Contains($UserPrivilege);
			}
		}

	}

	//entity for Menu Container outputs <ul><li>items 1,2,...n</li></ul>
	class MenuList{

		private $MenuItems = Array();
		private $index = 0;

		//Adds the menu to list
		function AddMenuItem($menuItem){
			$this->MenuItems[$this->index] = $menuItem;
			$this->index++;
		}

		//Displays List
		function Display($UserInfo){
			$counter = 0; //counts the menu items included
			$output = "<ul class=\"main-menu\">";
			//populate with list
			foreach($this->MenuItems as $MenuItem){
				if($MenuItem->CheckPrivilege($UserInfo->privileges) == true){

					if($counter == 0){
						$output .= "<li><a href=\"" . $MenuItem->url . "\" id=\"" . $MenuItem->id . "\" class=\"first\" title=\"" . $MenuItem->title . "\">" . $MenuItem->display . "</a></li>";
					} else {
						$output .= "<li><a href=\"" . $MenuItem->url . "\" id=\"" . $MenuItem->id . "\" class=\"\" title=\"" . $MenuItem->title . "\">" . $MenuItem->display . "</a></li>";
					}
					$counter++;

				}
			}

			$output .= "</ul>";

			return $output;
		}
	}

	//Home menu
	$Home = new MenuItem("Home","isms.php","Please Define", "menu-home");
	//User Menu
	$UsersPrivileges = new MenuPrivileges();
	$UsersPrivileges->AddPrivilege("SUPERADMIN");
	$UsersPrivileges->AddPrivilege("Users - Administrator");
	$UsersPrivileges->AddPrivilege("Users-Assign Rights");
	$Users = new MenuItem("Users","users.php","Please Define","menu-users",$UsersPrivileges);
	//Facilities Menu
	$FacPr = new MenuPrivileges();
	$FacPr->AddPrivilege("SUPERADMIN");
	$FacPr->AddPrivilege("Facilities - Administrator");
	$Fac = new MenuItem("Facilities","facilities.php", "Please Define", "menu-facilities", $FacPr);
	//Divisions Menu
	$DivPr = new MenuPrivileges();
	$DivPr->AddPrivilege("SUPERADMIN");
	$DivPr->AddPrivilege("Divisions - Administrator");
	$Div = new MenuItem("Divisions", "divisions.php", "Please Define", "menu-divisions", $DivPr);
	//Departments menu
	$DeptPr = new MenuPrivileges();
	$DeptPr->AddPrivilege("SUPERADMIN");
	$DeptPr->AddPrivilege("Departments - Administrator");
	$Dept = new MenuItem("Departments", "departments.php", "Please Define", "menu-departments", $DeptPr);
	//Colleges Menu
	$CollPr = new MenuPrivileges();
	$CollPr->AddPrivilege("SUPERADMIN");
	$CollPr->AddPrivilege("Colleges - Administrator");
	$Coll = new MenuItem("Colleges", "colleges.php", "Please Define", "menu-colleges", $CollPr);
	//Employment Menu
	$EmpPr = new MenuPrivileges();
	$EmpPr->AddPrivilege("SUPERADMIN");
	$EmpPr->AddPrivilege("Employment - Administrator");
	$Emp = new MenuItem("Human Resources", "employment.php", "Please Define", "menu-employment", $EmpPr);
	//School++
	$SchPr = new MenuPrivileges();
	$SchPr->AddPrivilege("SUPERADMIN");
	$SchPr->AddPrivilege("School - Administrator");
	$Sch = new MenuItem("School++", "schoolplusplus.php", "Please Define", "menu-schoolplusplus", $SchPr);
	//General Information
	$GenPr = new MenuPrivileges();
	$GenPr->AddPrivilege("SUPERADMIN");
	$GenPr->AddPrivilege("General Information - Administrator");
	$Gen = new MenuItem("General Info", "generalinfo.php", "Please Define", "menu-general", $GenPr);
	//Students - Admission - and Student Permanent Records
	$StuPr = new MenuPrivileges();
	$StuPr->AddPrivilege("SUPERADMIN");
	$StuPr->AddPrivilege("Student - Administrator");
	$Stu = new MenuItem("Students", "students.php", "Please Define", "menu-students", $StuPr);
	//Schedule Administration
	$SchePr = new MenuPrivileges();
	$SchePr->AddPrivilege("SUPERADMIN");
	$SchePr->AddPrivilege("Schedules - Administrator");
	$Sche = new MenuItem("Schedules", "schedules.php", "Please Define", "menu-schedules", $SchePr);
	//Enlistment Administration
	$EnlPr = new MenuPrivileges();
	$EnlPr->AddPrivilege("SUPERADMIN");
	$EnlPr->AddPrivilege("Enlistment - Administrator");
	$Enl = new MenuItem("Enlistment", "enlistment.php", "Please Define", "menu-enlistment", $EnlPr);
	//Grades Administration
	$GrdPr = new MenuPrivileges();
	$GrdPr->AddPrivilege("SUPERADMIN");
	$GrdPr->AddPrivilege("Grades - Administrator");
	$GrdPr->AddPrivilege("Grades - Encoder");
	$GrdPr->AddPrivilege("Grades - Viewer");
	$Grd = new MenuItem("Grades", "grades.php", "Please Define", "menu-grades", $GrdPr);
	//Reports Administration
	$RepPr = new MenuPrivileges();
	$RepPr->AddPrivilege("SUPERADMIN");
	$RepPr->AddPrivilege("Reports - Administrator");
	$Rep = new MenuItem("Reports", "reports.php", "Please Define", "menu-reports", $RepPr);
	//Finance Administration
	$FinPr = new MenuPrivileges();
	$FinPr->AddPrivilege("SUPERADMIN");
	$FinPr->AddPrivilege("Finance - Administrator");
	$Fin = new MenuItem("Finance", "finance.php", "Please Define", "menu-finance", $FinPr);

	//Statistics
	$StatPr = new MenuPrivileges();
	$StatPr->AddPrivilege("SUPERADMIN");
	$StatPr->AddPrivilege("Statistics - Administrator");
	$StatPr->AddPrivilege("School Owner");
	$Stat = new MenuItem("Statistics", "statistics.php", "Please Define", "menu-statistics", $StatPr);

	//Others Administration
	$FinPr2 = new MenuPrivileges();
	$FinPr2->AddPrivilege("SUPERADMIN");
	$FinPr2->AddPrivilege("Others - Administrator");
	$Fin2 = new MenuItem("&nbsp;", "ecnanif.php", "", "menu-finance", $FinPr2);

	//Others Administration
	$FinPr3 = new MenuPrivileges();
	$FinPr3->AddPrivilege("SUPERADMIN");
	$FinPr3->AddPrivilege("Others - Administrator");
	$Fin3 = new MenuItem("&nbsp;", "#", "", "", $FinPr3);

	//Audit Trail Administration
	$AuditPr = new MenuPrivileges();
	$AuditPr->AddPrivilege("SUPERADMIN");
	$AuditPr->AddPrivilege("School Owner");
	$Audit = new MenuItem("Audit Trail","audit-trail.php", "", "menu-audit", $AuditPr);

	$MAINMENU = new MenuList();
	$MAINMENU->AddMenuItem($Home);	//Home
	//$MAINMENU->AddMenuItem($Audit); //Audit Trail
	$MAINMENU->AddMenuItem($Users);	//Users
	$MAINMENU->AddMenuItem($Fac);	//Facilities
	//$MAINMENU->AddMenuItem($Div);	//Divisions
	//$MAINMENU->AddMenuItem($Dept);//Departments
	$MAINMENU->AddMenuItem($Coll);	//Colleges
	$MAINMENU->AddMenuItem($Emp);	//Employment
	$MAINMENU->AddMenuItem($Fin);	//Finance
	$MAINMENU->AddMenuItem($Gen);	//General Information
	$MAINMENU->AddMenuItem($Sch);	//School++
	$MAINMENU->AddMenuItem($Sche);	//Schedules
	$MAINMENU->AddMenuItem($Stu);	//Admission
	$MAINMENU->AddMenuItem($Enl);	//Enlistment
	$MAINMENU->AddMenuItem($Grd);	//Grade
	$MAINMENU->AddMenuItem($Rep);	//Grade
	//$MAINMENU->AddMenuItem($Stat);  //Statistics
	$MAINMENU->AddMenuItem($Fin3);	//Others
	$MAINMENU->AddMenuItem($Fin2);	//Others
	//$MAINMENU->AddMenuItem($Rep);	//Reports
?>
