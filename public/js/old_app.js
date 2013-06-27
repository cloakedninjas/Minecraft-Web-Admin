MWA = {
	server_control_lock: false,
	server_stop_timer: 15,

	init: function() {
		this.enableTabNavs();
		this.handleFlashMessages();
	},

	enableTabNavs: function() {
		$("ul.tab_nav a").click(function() {
			var elem = $(this);

			elem.parents("ul").find("li").removeClass("active");
			elem.parent().addClass("active");

			$("fieldset.tab").hide();

			var tab = $(this).attr('href').replace("#", "");

			$("fieldset.tab." + tab).show();
		});

		if (location.hash != '') {
			var link = $("ul.tab_nav a[href='" + location.hash + "']");

			if (link.length == 1) {
				link.click();
			}
		}
	},

	handleFlashMessages: function() {
		if ($("#flash_message div").length != 0) {

			$("#flash_message").animate({
				top: -1
			}, 1000);

			setTimeout(function() {
				$("#flash_message").animate({
					top: -50
				}, 1000, function() {
					$("#flash_message div").remove();
				});
			}, 4000);
		}
	},

	flashMessage: function(error, type) {
		if (typeof type == "undefined") {
			var type = "notice";
		}
		$("#flash_message").append("<div class=\"" + type + "\">" + error + "</div>");
		MWA.handleFlashMessages();
	},

	buildServer: function() {

		var req = new XMLHttpRequest();
		req.open('GET', '/setup?step=4', true);

		var dlp_marker = "#dlp#";
		var complete_marker = "#complete#";

		var len = 0;
		var dlp = null;
		var output = $(".console_output");
		var output_dlp = null;
		var output_perc = null;

		req.onreadystatechange=function() {
			if (req.responseText.length > len) {

				resp = req.responseText.substring(len);

				if (resp.indexOf(dlp_marker) == 0) {
					if (dlp === null) {
						output.append("<p class=\"dlp\"><span class=\"progress_meter\"></span><span class=\"percent\"></span></p>");
						output_dlp = $(".console_output .progress_meter");
						output_perc = $(".console_output .percent");
					}

					var dlps = resp.split(dlp_marker);
					dlp = dlps.pop();

					output_dlp.css("width", dlp+"%");
					output_perc.text(dlp + "%");

				}
				else if (resp.indexOf(complete_marker) == 0) {
					setTimeout(function() {
						document.location.href = "/";
					}, 2000);
				}
				else {
					var r = resp.split("\n");

					for (var i = 0; i < r.length; i++) {
						if (r[i] == "") {
							continue;
						}
						output.append("<p>" + r[i] + "</p>");
					}

				}

				len = req.responseText.length;
			}
		};

		req.send(null);
	},

	startServer: function(id) {
		if (this.server_control_lock) {
			return false;
		}

		this.server_control_lock = true;

		$(".status_light").hide();
		$(".status_text").text('Starting...');

		$.ajax({
			url: "/api/start-server",
			type: "POST",
			dataType: "json",
			data: {
				server_id: id
			},
			success: function(data) {
				MWA.server_control_lock = false;

				// check server is running
				setTimeout(function() {
					MWA.checkServerIsRunning(id);
				}, 2000);
			},
			error: function() {
				MWA.server_control_lock = false;

				console.log("plop");
			}
		});
	},

	stopServer: function(id, restart) {
		if (this.server_control_lock) {
			return false;
		}

		this.server_control_lock = true;
		var force_b = ($("#graceful_stop").attr("checked") == "checked") ? 0 : 1;

		$.ajax({
			url: "/api/stop-server",
			type: "POST",
			dataType: "json",
			data: {
				server_id: id,
				force: force_b
			},
			success: function(data) {
				MWA.server_control_lock = false;

				// check server is running
				var cb;
				if (restart) {
					cb =  function(running) {
						if (!running) {
							MWA.startServer(id);
						}
					};
				}
				else {
					cb = null;
				}

				setTimeout(function() {
					MWA.checkServerIsRunning(id, cb);
				}, 2000);
			},
			error: function() {
				MWA.server_control_lock = false;
				console.log("plop");
			}
		});

		if ($("#graceful_stop").attr("checked") != "checked") {
			$(".status_light").hide();
			$(".status_text").text('Stopping...');
		}
		else {
			$(".status_text").text('Stopping in ' + MWA.server_stop_timer + "...");

			var i = 0;
			var t;

			t = setInterval(function() {
				$(".status_text").text('Stopping in ' + (MWA.server_stop_timer - i) + "...");
				i++;

				if (i == MWA.server_stop_timer) {
					clearInterval(t);
					$(".status_text").text('Stopping...');
				}
			}, 1000);
		}
	},

	checkServerIsRunning: function(id, callback) {
		$.ajax({
			url: "/api/is-server-running/?server_id=" + id,
			type: "GET",
			dataType: "json",
			success: function(data) {
				if (data.running) {
					$(".status_light").addClass("status_ok").removeClass("status_nok").show();
					$(".status_text").text('Yes');
					$(".running").show();
					$(".stopped").hide();
				}
				else {
					$(".status_light").removeClass("status_ok").addClass("status_nok").show();
					$(".status_text").text('No');
					$(".stopped").show();
					$(".running").hide();
				}

				if (typeof callback == "function") {
					callback(data.running);
				}
			},
			error: function() {

			}
		});
	},

	openPlayerActions: function(elem) {
		$(".player_actions").show();
		
		var ul = $(elem).parents("ul");
		var li = $(elem).parent();

		ul.find("li").removeClass("active");
		li.addClass("active");
	},

	issueCommand: function(cmd) {
		var data = {
			player: null,
			cmd: cmd,
			server_id: MWA.current_server
		};
		
		data.player = $("ul.player_list li.active").data("player");

		if (data.player == null) {
			MWA.flashMessage("Could not find chosen player", "warn");
			MWA.closeWindow();
			return;
		}
		
		var cb = false;
		
		switch (cmd) {
			case "tell":
				data.message = $("#tell_window input:eq(0)").val();
				cb = function() {
					$("#tell_window input:eq(0)").val("");
					MWA.flashMessage("Whisper sent");
				};
				break;
				
			case "give":
				data.message = $("#give_window input:eq(0)").val();
				cb = function() {
					$("#tell_window input:eq(0)").val("");
					MWA.flashMessage("Whisper sent");
				};
				break;
				
			case "whitelist":
				data.method = "add";
				break;
		}
		
		$.ajax({
			url: "/api/issue-command",
			type: "POST",
			data: data,
			dataType: "json",
			success: function(data) {
				if (typeof data.error != "undefined") {
					MWA.flashMessage(data.error, "warn");
				}
				else {
					if (typeof cb == "function") {
						cb();
						MWA.closeWindow();
					}
				}
			},
			error: function() {
				MWA.closeWindow();
			}
		});
		
	},

	openWindow: function(type) {
		var win = $("#" + type + "_window");
		
		if (win.length > 0) {
			$("body").append("<div id=\"fade_out\"></div>");

			win.appendTo("body");
			var top = ($(document).height() / 2) - win.height();
			win.css("top", top);
			
			win.find("input:eq(0)").focus();
		
		}
	},
	
	closeWindow: function(elem) {
		$(".dialog").appendTo("#windows");
		$("#fade_out").remove();
	},

  formErrors: function(errors) {
    console.log(errors)
  }

};

$(document).ready(function() {
	MWA.init();
});