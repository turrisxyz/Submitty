<?php

namespace app\views;

use app\models\User;

class NotificationView extends AbstractView {
    public function showNotifications($current_course, $show_all, $notifications, $notification_saves) {
        $this->core->getOutput()->addBreadcrumb("Notifications");
        $this->core->getOutput()->addInternalCss('notifications.css');
        $this->core->getOutput()->enableMobileViewport();
        $this->core->getOutput()->renderTwigOutput("Notifications.twig", [
            'course' => $current_course,
            'show_all' => $show_all,
            'notifications' => $notifications,
            'notification_saves' => $notification_saves,
            'notifications_url' => $this->core->buildCourseUrl(['notifications']),
            'mark_all_as_seen_url' => $this->core->buildCourseUrl(['notifications', 'seen']),
            'notification_settings_url' => $this->core->buildCourseUrl(['notifications', 'settings'])
        ]);
    }

    public function showNotificationSettings($notification_saves) {
        $this->core->getOutput()->addBreadcrumb("Notifications", $this->core->buildCourseUrl(['notifications']));
        $this->core->getOutput()->addInternalCss('notifications.css');
        $this->core->getOutput()->addBreadcrumb("Notification Settings");
        $this->core->getOutput()->renderTwigOutput("NotificationSettings.twig", [
            'notification_saves' => $notification_saves,
            'email_enabled' => $this->core->getConfig()->isEmailEnabled(),
            'csrf_token' => $this->core->getCsrfToken(),
            'defaults' => User::constructNotificationSettings([]),
            'update_settings_url' => $this->core->buildCourseUrl(['notifications', 'settings']),
            'settings' => [
                [
                    "header" => "Forum",
                    "options" => array_filter([
                        [
                            "key" => "announcement",
                            "title" => "New Announcement",
                            "description" => "Alert me when an instructor posts a new announcement",
                            "disabled" => true
                        ],
                        [
                            "key" => "reply_thread",
                            "title" => "Reply to My Thread",
                            "description" => "Alert me when a reply is posted in a thread that I created",
                            "disabled" => true
                        ],
                        [
                            "key" => "my_post_altered",
                            "title" => "My Post was Modified",
                            "description" => "Alert me when one of my posts is edited, deleted, or merged",
                            "disabled" => true
                        ],
                        [
                            "key" => "reply_in_post_thread",
                            "title" => "Reply in Participating Thread",
                            "description" => "Alert me when a reply is posted in a thread in which I also posted",
                            "disabled" => false
                        ],
                        [
                            "key" => "merge_threads",
                            "title" => "Merged Thread",
                            "description" => "Alert me when a thread is merged",
                            "disabled" => false
                        ],
                        [
                            "key" => "all_new_threads",
                            "title" => "All New Threads",
                            "description" => "Alert me when a thread is created",
                            "disabled" => false
                        ],
                        [
                            "key" => "all_new_posts",
                            "title" => "All New Posts",
                            "description" => "Alert me when a post is created",
                            "disabled" => false
                        ],
                        // By setting this value to false when we dont want it, the call to array_filter above removes it from the array
                        $this->core->getUser()->accessFullGrading() ? [
                            "key" => "all_modifications_forum",
                            "title" => "All Modified Threads & Posts",
                            "description" => "Alert me when a thread/post has been edited, deleted, or undeleted",
                            "disabled" => false
                        ] : false,
                    ]),
                ],
                [
                    "header" => "Grade Inquiry",
                    "options" => [
                        [
                            "key" => "new_grade_inquiry",
                            "title" => "Grade Inquiry Submitted",
                            "description" => $this->core->getUser()->accessGrading() ? "Alert me when a student that I graded submits a grade inquiry" : "Alert me when my team member or grader makes a grade inquiry on my work",
                            "disabled" => true
                        ],
                        [
                            "key" => "new_grade_inquiry_post",
                            "title" => "Grade Inquiry Post",
                            "description" => $this->core->getUser()->accessGrading() ? "Alert me when a student posts a followup message on their grade inquiry" : "Alert me when my team member or grader posts a followup message on my grade inquiry",
                            "disabled" => true
                        ],
                        [
                            "key" => "grade_inquiry_resolved",
                            "title" => "Grade Inquiry Resolved",
                            "description" => $this->core->getUser()->accessGrading() ? "Alert me when a student closes their grade inquiry" : "Alert me when a team member or grader resolves my grade inquiry",
                            "disabled" => true
                        ],
                        [
                            "key" => "grade_inquiry_reopened",
                            "title" => "Grade Inquiry Re-Opened",
                            "description" => $this->core->getUser()->accessGrading() ? "Alert me when a student reopens their grade inquiry" : "Alert me when a team member or grader reopens my grade inquiry",
                            "disabled" => true
                        ],
                    ],
                ],
                [
                    "header" => "Team",
                    "options" => [
                        [
                            "key" => "team_invite",
                            "title" => "Team Invitation",
                            "description" => "Alert me when I get an invitation to join a team",
                            "disabled" => false
                        ],
                        [
                            "key" => "team_joined",
                            "title" => "New Team Member",
                            "description" => "Alert me when a new team member joins my team",
                            "disabled" => false
                        ],
                        [
                            "key" => "team_member_submission",
                            "title" => "Team Member Submission",
                            "description" => "Alert me when a team member makes a submission to the gradeable",
                            "disabled" => false
                        ],
                    ],
                ],
                [
                    "header" => "",
                    "options" => [
                        [
                            "key" => "self_notification",
                            "title" => "My Actions/Updates",
                            "description" => "Alert me when I perform the actions selected above",
                            "disabled" => false
                        ],
                    ],
                ]
            ]
        ]);
    }
}
