/*
This file is part of iCE Hrm.

iCE Hrm is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

iCE Hrm is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with iCE Hrm. If not, see <http://www.gnu.org/licenses/>.

------------------------------------------------------------------

Original work Copyright (c) 2012 [Gamonoid Media Pvt. Ltd]  
Developer: Thilina Hasantha (thilina.hasantha[at]gmail.com / facebook.com/thilinah)
 */

function EmployeeImmigrationAdapter(endPoint) {
	this.initAdapter(endPoint);
}

EmployeeImmigrationAdapter.inherits(AdapterBase);



EmployeeImmigrationAdapter.method('getDataMapping', function() {
	return [
	        "id",
	        "document",
	        "doc_number",
	        "issued",
	        "expiry",
	        "status",
	        "details"
	];
});

EmployeeImmigrationAdapter.method('getHeaders', function() {
	return [
			{ "sTitle": "ID" ,"bVisible":false},
			{ "sTitle": "Document","sClass": "columnMain"},
			{ "sTitle": "Number"},
			{ "sTitle": "Issued Date"},
			{ "sTitle": "Expiry Date"},
			{ "sTitle": "Status"},
			{ "sTitle": "Details"}
	];
});

EmployeeImmigrationAdapter.method('getFormFields', function() {
	return [
	        [ "id", {"label":"ID","type":"hidden"}],
	        [ "document", {"label":"Document","type":"select2","source":[["Passport","Passport"],["Visa","Visa"]]}],
	        [ "doc_number", {"label":"Number","type":"text","validation":""}],
	        [ "issued", {"label":"Issued Date","type":"date","validation":""}],
	        [ "expiry", {"label":"Expiry Date","type":"date","validation":""}],
	        [ "status", {"label":"Status","type":"text","validation":"none"}],
	        [ "details", {"label":"Details","type":"textarea","validation":"none"}]
	];
});
