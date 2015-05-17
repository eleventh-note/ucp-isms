var hoverColor = "#d2faed";
var unhoverColor = "";

//## Activates the hover effects for tables
function ActivateTableHoverEffects(){
	$(document).ready(
		//####> FUNCTION FOR HANDLING HOVER AND UNHOVERS
		function(){
		
			$(".odd").hover(
				function(){
					unhoverBorderColor = $(this).css("border-color");
					unhoverColor = $(this).css("background-color");
					$(this).css("background-color", hoverColor);
				},
				function(){
					$(this).css("background-color", unhoverColor);
				}
			)
			
			$(".even").hover(
				function(){
					unhoverColor = $(this).css("background-color");
					$(this).css("background-color", hoverColor);
				},
				function(){
					$(this).css("background-color", unhoverColor);
				}
			)
			
		}
	);
}

// Reports->Grades->Viewer
function openReportsGradeViewer(studentId) {
	var syId = $('#oReportGradesViewerSy').val();
	var semesterId = $('#oReportGradesViewerSem').val();
	//alert('reports-grades-viewer-pdf.php?id=' + studentId + '&sy=' + syId + '&semester=' + semesterId);
	window.open('reports-grades-viewer-pdf.php?id=' + studentId + '&sy=' + syId + '&semester=' + semesterId);
}

ActivateTableHoverEffects();