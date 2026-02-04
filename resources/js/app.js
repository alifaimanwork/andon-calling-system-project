// Axios
import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// jQuery
window.$ = window.jQuery = require('jquery');

// moment
window.moment = require('moment-timezone');

// Bootstrap
//import "bootstrap";
window.bootstrap = require('bootstrap');

// chart.js
import Chart from 'chart.js/auto';
import 'chartjs-adapter-moment';

window.Chart = Chart;



// Chart js datalabels plugin
import ChartDataLabels from 'chartjs-plugin-datalabels';
window.ChartDataLabels = ChartDataLabels;

// Chart js annotations plugin
import annotationPlugin from 'chartjs-plugin-annotation';
window.annotationPlugin = annotationPlugin;
Chart.register(annotationPlugin);

// Chart js chartjs-plugin-zoom plugin
import zoomPlugin from 'chartjs-plugin-zoom';
Chart.register(zoomPlugin);

// Datatables

import "datatables.net/js/jquery.dataTables";
import "datatables.net-bs5/js/dataTables.bootstrap5";

// Daterange picker
import "daterangepicker/daterangepicker";


// Custom Components
import { PageDatatableList } from "./components/PageDatatableList";
window.PageDatatableList = PageDatatableList;

import { DataTableLoader } from "./components/DataTableLoader";
window.DataTableLoader = DataTableLoader;

import { CsrfKeepAlive } from "./components/CsrfKeepAlive";
window.csrf = new CsrfKeepAlive();



import { BootstrapModalDialog } from './components/BootstrapModalDialog';
window.ModalDialog = BootstrapModalDialog;

import { LiveClock } from './components/LiveClock';
window.LiveClock = LiveClock;

import { LiveProduction } from './components/LiveProduction';
window.LiveProduction = LiveProduction;

import { ColorGenerator } from './components/ColorGenerator';
window.ColorGenerator = ColorGenerator;