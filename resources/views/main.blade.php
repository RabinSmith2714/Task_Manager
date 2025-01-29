<?php

use routes\web;
use App\Http\Controllers\userController;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MIC</title>
    <link rel="icon" type="image/png" sizes="32x32" href="image/icons/mkce_s.png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs/build/css/alertify.min.css" />
    <link href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-collapsed-width: 70px;
            --topbar-height: 60px;
            --footer-height: 60px;
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --dark-bg: #1a1c23;
            --light-bg: #f8f9fc;
            --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* General Styles with Enhanced Typography */
        body {
            min-height: 100vh;
            margin: 0;
            background: var(--light-bg);
            overflow-x: hidden;
            padding-bottom: var(--footer-height);
            position: relative;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        /* Content Area Styles */
        .content {
            margin-left: var(--sidebar-width);
            padding-top: var(--topbar-height);
            transition: all 0.3s ease;
            min-height: 100vh;
        }

        /* Content Navigation */
        .content-nav {
            background: linear-gradient(45deg, #4e73df, #1cc88a);
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .content-nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            gap: 20px;
            overflow-x: auto;
        }

        .content-nav li a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .content-nav li a:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .sidebar.collapsed+.content {
            margin-left: var(--sidebar-collapsed-width);
        }

        .breadcrumb-area {
            background: white;
            border-radius: 10px;
            box-shadow: var(--card-shadow);
            margin: 20px;
            padding: 15px 20px;
        }

        .breadcrumb-item a {
            color: var(--primary-color);
            text-decoration: none;
            transition: var(--transition);
        }

        .breadcrumb-item a:hover {
            color: #224abe;
        }

        /* Table Styles */
        .custom-table {
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .custom-table thead {
            background: linear-gradient(135deg, #4CAF50, #2196F3);
            text-align: center;

        }

        .gradient-header {

            --bs-table-bg: transparent;

            --bs-table-color: white;

            background: linear-gradient(135deg, #4CAF50, #2196F3) !important;

            text-align: center;

            font-size: 0.9em;
        }

        .custom-table th {
            font-weight: 500;
            padding: 15px;
            text-align: center;
        }


        .custom-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            text-align: center;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: var(--sidebar-width) !important;
            }

            .sidebar.mobile-show {
                transform: translateX(0);
            }

            .topbar {
                left: 0 !important;
            }

            .mobile-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 999;
                display: none;
            }

            .mobile-overlay.show {
                display: block;
            }

            .content {
                margin-left: 0 !important;
            }

            .brand-logo {
                display: block;
            }

            .user-profile {
                margin-left: 0;
            }

            .sidebar .logo {
                justify-content: center;
            }

            .sidebar .menu-item span,
            .sidebar .has-submenu::after {
                display: block !important;
            }

            body.sidebar-open {
                overflow: hidden;
            }

            .footer {
                left: 0 !important;
            }

            .content-nav ul {
                flex-wrap: nowrap;
                overflow-x: auto;
                padding-bottom: 5px;
            }

            .content-nav ul::-webkit-scrollbar {
                height: 4px;
            }

            .content-nav ul::-webkit-scrollbar-thumb {
                background: rgba(255, 255, 255, 0.3);
                border-radius: 2px;
            }
        }

        .container-fluid {
            padding: 20px;
        }

        /* loader */
        .loader-container {
            position: fixed;
            left: var(--sidebar-width);
            right: 0;
            top: var(--topbar-height);
            bottom: var(--footer-height);
            background: rgba(255, 255, 255, 0.95);
            display: flex;
            /* Changed from 'none' to show by default */
            justify-content: center;
            align-items: center;
            z-index: 1000;
            transition: left 0.3s ease;
        }

        .sidebar.collapsed+.content .loader-container {
            left: var(--sidebar-collapsed-width);
        }

        @media (max-width: 768px) {
            .loader-container {
                left: 0;
            }
        }

        /* Hide loader when done */
        .loader-container.hide {
            display: none;
        }

        /* Loader Animation */
        .loader {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-radius: 50%;
            border-top: 5px solid var(--primary-color);
            border-right: 5px solid var(--success-color);
            border-bottom: 5px solid var(--primary-color);
            border-left: 5px solid var(--success-color);
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Hide content initially */
        .content-wrapper {
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        /* Show content when loaded */
        .content-wrapper.show {
            opacity: 1;
        }

        /*image modal */
        .modal {
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(5px);
        }

        .modal-dialog {
            transition: all 0.3s ease-in-out;
            transform: scale(0.7);
            opacity: 0;
        }

        .modal.show .modal-dialog {
            transform: scale(1);
            opacity: 1;
        }

        .modal-content {
            border-radius: 15px;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.2);
            background: linear-gradient(145deg, #f0f0f0, #ffffff);
            border: none;
            overflow: hidden;
        }

        .modal-header {
            background: linear-gradient(to right, #6a11cb 0%, #2575fc 100%);
            color: white;
            padding: 15px 20px;
            border-bottom: none;
        }

        .modal-header .modal-title {
            font-weight: 600;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .modal-header .btn-close {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            opacity: 1;
            width: 30px;
            height: 30px;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23ffffff'%3e%3cpath d='M.293.293a1 1 0 011.414 0L8 6.586 14.293.293a1 1 0 111.414 1.414L9.414 8l6.293 6.293a1 1 0 01-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 01-1.414-1.414L6.586 8 .293 1.707a1 1 0 010-1.414z'/%3e%3c/svg%3e");
            background-size: 30%;
            background-position: center;
            background-repeat: no-repeat;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .modal-header .btn-close:hover {
            background-color: rgba(255, 255, 255, 0.4);
            transform: scale(1.1);
        }

        .modal-header .btn-close:focus {
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.3);
            outline: none;
        }

        .modal-body {
            padding: 20px;
            background: #f8f9fa;
        }

        .modal-body p {
            margin-bottom: 10px;
            color: #333;
        }

        .modal-body p strong {
            color: #2575fc;
        }

        .modal-body .badge {
            font-size: 0.9em;
            padding: 5px 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Animate entrance */
        @keyframes modalEnter {
            0% {
                opacity: 0;
                transform: scale(0.7) translateY(-50px);
            }

            100% {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .modal.show .modal-dialog {
            animation: modalEnter 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
        }

        /* Topbar Styles */
        .topbar {
            position: fixed;
            top: 0;
            right: 0;
            left: var(--sidebar-width);
            height: var(--topbar-height);
            /* background-color: #E4E4E1; */
            background: linear-gradient(to bottom, rgba(255, 255, 255, 0.15) 0%, rgba(0, 0, 0, 0.15) 100%), radial-gradient(at top center, rgba(255, 255, 255, 0.40) 0%, rgba(0, 0, 0, 0.40) 120%) #989898;
            background-blend-mode: multiply, multiply;

            box-shadow: var(--card-shadow);
            display: flex;
            align-items: center;
            padding: 0 20px;
            transition: all 0.3s ease;
            z-index: 999;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .brand-logo {
            display: none;
            color: var(--primary-color);
            font-size: 24px;
            margin: 0 auto;
        }

        .sidebar.collapsed+.content .topbar {
            left: var(--sidebar-collapsed-width);
        }

        .hamburger {
            cursor: pointer;
            font-size: 20px;
            color: white;
        }

        .user-profile {
            margin-left: auto;
            color: white;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            position: relative;
            transition: var(--transition);
            border: 2px solid var(--primary-color);
        }

        .user-avatar:hover {
            transform: scale(1.1);
        }

        .online-indicator {
            position: absolute;
            width: 10px;
            height: 10px;
            background: var(--success-color);
            border-radius: 50%;
            bottom: 0;
            right: 0;
            animation: blink 1.5s infinite;
        }

        @keyframes blink {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }

            100% {
                opacity: 1;
            }
        }

        /* User Menu Dropdown */
        .user-menu {
            position: relative;
            cursor: pointer;
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            display: none;
            min-width: 200px;
        }

        .dropdown-menu.show {
            display: block;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-10px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .dropdown-item {
            padding: 10px 20px;
            color: var(--secondary-color);
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
        }

        .dropdown-item:hover {
            background: var(--light-bg);
            color: var(--primary-color);
        }

        /* User Profile Styles */
        .user-profile {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .online-indicator {
            position: absolute;
            width: 10px;
            height: 10px;
            background: var(--success-color);
            border-radius: 50%;
            bottom: 0;
            right: 0;
            border: 2px solid white;
            animation: blink 1.5s infinite;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: var(--dark-bg);
            transition: var(--transition);
            z-index: 1000;
            overflow-y: auto;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            background-image: url('image/pattern_h.png');
        }

        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
        }

        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        .sidebar .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 20px;
            color: white;
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar .logo img {
            max-height: 90px;
            width: auto;
        }

        .sidebar .s_logo {
            display: none;
        }

        .sidebar.collapsed .logo img {
            display: none;
        }

        .sidebar.collapsed .logo .s_logo {
            display: flex;
            max-height: 50px;
            width: auto;
            align-items: center;
            justify-content: center;
        }

        .sidebar .menu {
            padding: 10px;
        }

        .menu-item {
            padding: 12px 15px;
            color: rgba(255, 255, 255, 0.7);
            display: flex;
            align-items: center;
            cursor: pointer;
            border-radius: 5px;
            margin: 4px 0;
            transition: all 0.3s ease;
            position: relative;
            text-decoration: none;
        }

        .menu-item:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .menu-item i {
            min-width: 30px;
            font-size: 18px;
        }

        .menu-item span {
            margin-left: 10px;
            transition: all 0.3s ease;
            flex-grow: 1;
        }

        .has-submenu::after {
            content: '\f107';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            margin-left: 10px;
            transition: transform 0.3s ease;
        }

        .has-submenu.active::after {
            transform: rotate(180deg);
        }

        .sidebar.collapsed .menu-item span,
        .sidebar.collapsed .has-submenu::after {
            display: none;
        }

        .submenu {
            margin-left: 30px;
            display: none;
            transition: all 0.3s ease;
        }

        .submenu.active {
            display: block;
        }

        .custom-tabs {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 15px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
        }

        .nav-tabs {
            border: none;
            gap: 10px;
            padding: 6px;
            background: #f8f9fd;
            border-radius: 12px;
        }

        .nav-link {
            border: none !important;
            border-radius: 10px !important;
            padding: 10px 20px !important;
            font-weight: 600 !important;
            font-size: 0.95rem;
            letter-spacing: 0.3px;
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
            z-index: 1;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: inherit;
            z-index: -1;
            transform: translateY(100%);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .nav-link:hover::before {
            transform: translateY(0);
        }

        .nav-link.active {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        /* dashboard Tab Styling */
        #dash-bus-tab {
            background: linear-gradient(135deg, #FF6B6B, #FFE66D);
            color: #fff;
        }

        #dash-bus-tab:not(.active) {
            background: #fff;
            color: #FF6B6B;
        }

        #dash-bus-tab:hover:not(.active) {
            background: linear-gradient(135deg, #FF6B6B, #FFE66D);
            color: #fff;
        }

        /* pending Bus Tab Styling */
        #pend-bus-tab {
            background: linear-gradient(135deg, #4E65FF, #92EFFD);
            color: #fff;
        }

        #pend-bus-tab:not(.active) {
            background: #fff;
            color: #4E65FF;
        }

        #pend-bus-tab:hover:not(.active) {
            background: linear-gradient(135deg, #4E65FF, #92EFFD);
            color: #fff;
        }

        /* work in progress tab styling */
        #work-bus-tab {
            background: linear-gradient(135deg, rgb(99, 23, 23), rgb(222, 77, 77));
            color: #fff;
        }

        #work-bus-tab:not(.active) {
            background: #fff;
            color: rgb(99, 23, 23);
        }

        #work-bus-tab:hover:not(.active) {
            background: linear-gradient(135deg, rgb(99, 23, 23), rgb(222, 77, 77));
            color: #fff;
        }

        /* completed tab styling */

        #comp-bus-tab {
            background: linear-gradient(135deg, #065729, #09da9b);
            color: #fff;
        }

        #comp-bus-tab:not(.active) {
            background: #fff;
            color: #065729;
        }

        #comp-bus-tab:hover:not(.active) {
            background: linear-gradient(135deg, #065729, #09da9b);
            color: #fff;
        }

        /* rejected tab styling */

        #rej-bus-tab {
            background: linear-gradient(135deg, #434047, #d9e1de);
            color: #fff;
        }

        #rej-bus-tab:not(.active) {
            background: #fff;
            color: #434047;
        }

        #rej-bus-tab:hover:not(.active) {
            background: linear-gradient(135deg, #434047, #d9e1de);
            color: #fff;
        }

        /* reassigned tab styling */
        #res-bus-tab {
            background: linear-gradient(135deg, #51045a, #a859e0);
            color: #fff;
        }

        #res-bus-tab:not(.active) {
            background: #fff;
            color: #51045a;
        }

        #res-bus-tab:hover:not(.active) {
            background: linear-gradient(135deg, #51045a, #a859e0);
            color: #fff;
        }

        .tab-icon {
            margin-right: 8px;
            font-size: 1.1em;
            transition: transform 0.3s ease;
        }

        .nav-link:hover .tab-icon {
            transform: rotate(15deg) scale(1.1);
        }

        .nav-link.active .tab-icon {
            animation: bounce 0.5s ease infinite alternate;
        }

        @keyframes bounce {
            from {
                transform: translateY(0);
            }

            to {
                transform: translateY(-2px);
            }
        }

        .tab-content {
            padding: 20px;
            margin-top: 15px;
            background: #fff;
            border-radius: 12px;
            min-height: 200px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            position: relative;
        }

        .tab-pane {
            opacity: 0;
            transform: translateY(15px);
            transition: all 0.4s ease-out;
        }

        .tab-pane.active {
            opacity: 1;
            transform: translateY(0);
        }

        /* Glowing effect on active tab */
        .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 50%;
            transform: translateX(-50%);
            width: 40%;
            height: 3px;
            background: inherit;
            border-radius: 6px;
            filter: blur(2px);
            animation: glow 1.5s ease-in-out infinite alternate;
        }

        @keyframes glow {
            from {
                opacity: 0.6;
                width: 40%;
            }

            to {
                opacity: 1;
                width: 55%;
            }
        }

        /* Footer Styles */
        .footer {
            position: fixed;
            bottom: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--footer-height);
            background: linear-gradient(135deg, #2196F3, #4CAF50);
            color: linear-gradient(to bottom, rgba(255, 255, 255, 0.15) 0%, rgba(0, 0, 0, 0.15) 100%), radial-gradient(at top center, rgba(255, 255, 255, 0.40) 0%, rgba(0, 0, 0, 0.40) 120%) #989898;
            background-blend-mode: multiply, multiply;
            ;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            transition: all 0.3s ease;
            z-index: 999;
        }

        .sidebar.collapsed+.content .footer {
            left: var(--sidebar-collapsed-width);
        }

        .footer-links {
            display: flex;
            gap: 20px;
        }

        .footer-links a {
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .footer-links a:hover {
            opacity: 0.8;
        }

        /* dashboard */
        .circle-card {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: auto;
            color: white;
            position: relative;
            background: transparent;
            animation: fadeIn 1s ease-in-out;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .circle-card::before,
        .circle-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border: 2px solid rgba(255, 255, 255, 0.5);
            border-radius: 50%;
            animation: rotate 4s linear infinite;
        }

        .circle-card::after {
            border: 2px dashed rgba(255, 255, 255, 0.5);
            animation-duration: 6s;
            animation-direction: reverse;
        }

        .circle-card:hover {
            transform: scale(1.1);
        }

        .circle-card i {
            font-size: 2rem;
            margin-bottom: 5px;
        }

        .circle-card h1 {
            font-size: 1.8rem;
            margin: 0;
        }

        .circle-card small {
            font-size: 0.875rem;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.8);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .btn-success:disabled {
            background-color: #28a745 !important;
            border-color: #28a745 !important;
            color: #fff !important;
        }

        .btn-secondary:disabled {
            background-color: #6c757d !important;
            border-color: #6c757d !important;
            color: #fff !important;
        }

        /*star rating*/
        .stars span {
            font-size: 2rem;
            cursor: pointer;
            color: gray;
            /* Default color for unlit stars */
            transition: color 0.3s;
        }

        .stars span.highlighted {
            color: gold;
            /* Color for lit stars */
        }

        /* breadcrumb style */
        .breadcrumb-area {
            background: linear-gradient(45deg, #6a11cb, #2575fc);
            /* Gradient colors */
            padding: 10px 20px;
            /* Add padding for spacing */
            border-radius: 8px;
            /* Rounded corners */
        }

        .breadcrumb a {
            color: #fff;
            /* Link text color */
            text-decoration: none;
        }

        .breadcrumb .breadcrumb-item.active {
            color: #f0f0f0;
            /* Active item text color */
            font-weight: bold;
        }

        .breadcrumb {
            margin-bottom: 0;
            /* Prevent extra margin at the bottom */
        }

        .dropdown-content {
            display: none;
            background-color: #f9f9f9;
            border: 1px solid #ccc;
            max-height: 200px;
            overflow-y: auto;
            position: absolute;
            width: 100%;
            z-index: 1000;
        }

        .dropdown-content label {
            display: block;
            padding: 5px;
            cursor: pointer;
        }

        .dropdown-content label:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="mobile-overlay" id="mobileOverlay"></div>
    <div class="sidebar" id="sidebar">
        <div class="logo">
            <img src="image/mkce.png" alt="College Logo">
            <img class='s_logo' src="image/mkce_s.png" alt="College Logo">
        </div>
        <div class="menu">
            <a href="/dashboard" class="menu-item">
                <i class="fas fa-home text-primary"></i>
                <span>Dashboard</span>
            </a>
            <a href="" class="menu-item">
                <i class="fas fa-clipboard-question text-warning"></i>
                <span>Complaints</span>
            </a>

        </div>
    </div>
    <!-- Main Content -->
    <div class="content">
        <div class="loader-container" id="loaderContainer">
            <div class="loader"></div>
        </div>
        <!-- Topbar -->
        <div class="topbar">
            <div class="hamburger" id="hamburger">
                <i class="fas fa-bars"></i>
            </div>
            <!-- <div class="brand-logo">
                       <i class="fas fa-chart-line"></i>
                       MIC
                   </div> -->
            <div class="user-profile">
                <div class="user-menu" id="userMenu">
                    <div class="user-avatar">
                        <img src="image/icons/mkce_s.png" alt="User">
                        <div class="online-indicator"></div>
                    </div>
                    <div class="dropdown-menu">
                        <a class="dropdown-item">
                            <i class="fas fa-key"></i>
                            Change Password
                        </a>
                        <a class="dropdown-item">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout
                        </a>
                    </div>
                </div>
                <span>Faculty</span>
            </div>
        </div>
        <!-- Breadcrumb -->
        <div class="breadcrumb-area">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Complaints</li>
                </ol>
            </nav>
        </div>
        <!-- Content Area -->
        <div class="container-fluid">
            <!-- Sample Table -->
            <div id="navref ">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item " role="presentation">
                        <div id="navref2">
                            <button class="nav-link active" id="pend-bus-tab" data-bs-toggle="tab"
                                data-bs-target="#pending" type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">
                                <i class="fa-solid  fa-bell fa-shake "></i>&nbsp;Add Faculty</button>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="container-fluid">
                <div class="tab-content" id="myTabContent">
                    <!----------Pending Table -------------------------------------------------------------->
                    <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="contact-tab"
                        tabindex="0">
                        <div class="d-flex justify-content-end mb-3">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#addRole">
                                Add Role
                            </button>
                        </div>
                        <div class="custom-table table-responsive">
                            <table class="table table-hover mb-0 " id="AddRoles">
                                <thead class="gradient-header">
                                    <tr>
                                        <th>S.No</th>
                                        <th>Name</th>
                                        <th>Role</th>
                                        <th>Update</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $sno = 1 @endphp
                                    @foreach($improve as $up)
                                    <tr>

                                        <td>{{ $sno++ }}</td>
                                        <td>{{ $up->id }}</td>
                                        <td>{{ $up->Role }}</td>
                                        <td><button>View</button></td>

                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!---------------------------Add Role Modal ----------------------------------------------->
                    <div class="modal fade" id="addRole" tabindex="-1" aria-labelledby="exampleModalLabel">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Add Faculty</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="addroleform" enctype="multipart/form-data">


                                        <!-- fetch Roles -->
                                        <div class="mb-3">
                                            <label for="fid" class="form-label">Faculty ID</label>
                                            <input class="form-control" type="text" id="fid" name="fid" placeholder="Enter fid" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="workType" class="form-label">Type of Role</label>
                                            <select class="form-control" id="workType" name="workType" onchange="showDropdown()" required>
                                                <option value="">Select</option>
                                                @foreach($type as $t)
                                                <option value="{{ $t->type}}">{{ $t->type}}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mb-3" id="researchDropdown" style="display: none;">
                                            <label for="researchType" class="form-label">Management</label>
                                            <select class="form-control" id="researchType" onchange="setSelectedOption(this.value)">
                                                <option value="">Select</option>
                                                @foreach($management as $m)
                                                <option value="{{ $m->Rolename }}">{{ $m->Rolename }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mb-3" id="teachingDropdown" style="display: none;">
                                            <label for="teachingSubject" class="form-label"> Center of Heads </label>
                                            <select class="form-control" id="teachingSubject" onchange="setSelectedOption(this.value)">
                                                <option value="">Select</option>
                                                @foreach($centerofheads as $c)
                                                <option value="{{ $c->Rolename }}">{{ $c->Rolename }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Hidden input to store the selected option -->
                                        <input type="hidden" id="selectedOption" name="selectedOption" required>

                                        <!-- Simple Dropdown -->
                                        <div class="mb-3">
                                            <label for="simpleDropdown" class="form-label">Status</label>
                                            <select class="form-control" id="simpleDropdown" name="simpleDropdown">
                                                <option value="">Select Option</option>
                                                <option value="1">Option 1</option>
                                                <option value="2">Option 2</option>
                                                <option value="3">Option 3</option>
                                            </select>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary" id="submitDepartments">Submit</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-------------------- Footer -------------------------------->
                    <footer class="footer">
                        <div class="footer-copyright" style="text-align: center;">
                            <p>Copyright © 2024 Designed by
                                <b><i>Technology Innovation Hub - MKCE. </i> </b>All rights reserved.
                            </p>
                        </div>
                        <div class="footer-links">
                            <a href="https://www.linkedin.com/company/technology-innovation-hub-mkce/"><i class="fab fa-linkedin"></i></a>
                        </div>
                    </footer>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs/build/css/themes/default.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/alertifyjs/build/alertify.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.js"></script>
    <!-- CSRF link -->
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>

    <script>
        // Function to toggle dropdown visibility

        const loaderContainer = document.getElementById('loaderContainer');

        function showLoader() {
            loaderContainer.classList.add('show');
        }

        function hideLoader() {
            loaderContainer.classList.remove('show');
        }
        //    automatic loader
        document.addEventListener('DOMContentLoaded', function() {
            const loaderContainer = document.getElementById('loaderContainer');
            const contentWrapper = document.getElementById('contentWrapper');
            let loadingTimeout;

            function hideLoader() {
                loaderContainer.classList.add('hide');
                contentWrapper.classList.add('show');
            }

            function showError() {
                console.error('Page load took too long or encountered an error');
                // You can add custom error handling here
            }
            // Set a maximum loading time (10 seconds)
            loadingTimeout = setTimeout(showError, 10000);
            // Hide loader when everything is loaded
            window.onload = function() {
                clearTimeout(loadingTimeout);
                // Add a small delay to ensure smooth transition
                setTimeout(hideLoader, 500);
            };
            // Error handling
            window.onerror = function(msg, url, lineNo, columnNo, error) {
                clearTimeout(loadingTimeout);
                showError();
                return false;
            };
        });
        // Toggle Sidebar
        const hamburger = document.getElementById('hamburger');
        const sidebar = document.getElementById('sidebar');
        const body = document.body;
        const mobileOverlay = document.getElementById('mobileOverlay');

        function toggleSidebar() {
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('mobile-show');
                mobileOverlay.classList.toggle('show');
                body.classList.toggle('sidebar-open');
            } else {
                sidebar.classList.toggle('collapsed');
            }
        }
        hamburger.addEventListener('click', toggleSidebar);
        mobileOverlay.addEventListener('click', toggleSidebar);
        // Toggle User Menu
        const userMenu = document.getElementById('userMenu');
        const dropdownMenu = userMenu.querySelector('.dropdown-menu');
        userMenu.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdownMenu.classList.toggle('show');
        });
        // Close dropdown when clicking outside
        document.addEventListener('click', () => {
            dropdownMenu.classList.remove('show');
        });
        // Toggle Submenu
        const menuItems = document.querySelectorAll('.has-submenu');
        menuItems.forEach(item => {
            item.addEventListener('click', () => {
                const submenu = item.nextElementSibling;
                item.classList.toggle('active');
                submenu.classList.toggle('active');
            });
        });
        // Handle responsive behavior
        window.addEventListener('resize', () => {
            if (window.innerWidth <= 768) {
                sidebar.classList.remove('collapsed');
                sidebar.classList.remove('mobile-show');
                mobileOverlay.classList.remove('show');
                body.classList.remove('sidebar-open');
            } else {
                sidebar.style.transform = '';
                mobileOverlay.classList.remove('show');
                body.classList.remove('sidebar-open');
            }
        });

        // Show image in modal
        $(document).on('click', '#viewImageButton', function() {
            var imageSrc = $('#preview_image').attr('src');
            if (imageSrc) {
                $('#preview_images').show();
            } else {
                alert('No image found');
            }
        });

        // Show image
        $(document).on('click', '.showImage', function() {
            var id = $(this).val();
            console.log(id);

            $.ajax({
                type: "GET",
                url: `/imgs/image/${id}`,
                dataType: "json",
                success: function(response) {
                    console.log(response);
                    if (response.status === 200) {

                        $('#modalImage').attr('src', '/before_image/' + response.data.images);
                        $('#imageModal').modal('show');
                    } else {
                        alert(response.message || 'An error occurred while retrieving the image.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error: ", xhr.responseText);
                    alert('An error occurred: ' + error + "\nStatus: " + status + "\nDetails: " + xhr
                        .responseText);
                }
            });
        });
        alertify.defaults.notifier.position = 'top-right';
        //Datatable
        new DataTable('#pending1');
        new DataTable('#workinprog1');
        new DataTable('#completed1');
        new DataTable('#rejected1');
        new DataTable('#reassigned1');


        function setSelectedOption(value) {
            document.getElementById('selectedOption').value = value; // Set hidden input value
        }

        function showDropdown() {
            const workType = document.getElementById('workType').value;

            // Hide both dropdowns initially
            document.getElementById('researchDropdown').style.display = 'none';
            document.getElementById('teachingDropdown').style.display = 'none';

            // Show the relevant dropdown based on workType selection
            if (workType === 'management') {
                document.getElementById('researchDropdown').style.display = 'block';
            } else if (workType === 'center of heads') {
                document.getElementById('teachingDropdown').style.display = 'block';
            }

            // Clear the hidden input when changing workType
            document.getElementById('selectedOption').value = '';
        }
    </script>


    <script>
        $(document).on('submit', '#addroleform', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                type: 'POST',
                url: "/add/role",   // default
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.status === 200) {
                        alertify.success(response.message);
                        $('#addroleform')[0].reset();
                        $('#addRole').modal('hide');
                        $('#AddRoles').load(location.href + ' #AddRoles');
                    } else {
                        alertify.error(response.message);
                    }
                }
            });
        });
    </script>

</body>

</html>