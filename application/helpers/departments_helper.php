<?php
defined('BASEPATH') or exit('No direct script access allowed');

function get_department_att()
{
  $ci = &get_instance();

  $third_DB   = $ci->load->database('attendance', TRUE);
  $level      = $ci->session->userdata('user_level');
  $dept_id    = $ci->session->userdata('user_dept_id');
  $ID         = array('1', '1190', '115');

  //if ($level == 'sa' || $level == 'admin') {
  if ($level == '1') {
    $third_DB->where_not_in('DEPTID', $ID);
    $third_DB->where_not_in('SUPDEPTID', '115');
  } else {
    $third_DB->where('DEPTID', $dept_id);
  }

  $third_DB->select('*');
  $third_DB->from('DEPARTMENTS');
  $third_DB->order_by('DEPTNAME', 'ASC');
  $query = $third_DB->get();

  return $query->result();
}

function get_department_name($id)
{
  $ci = &get_instance();

  $third_DB   = $ci->load->database('attendance', TRUE);

  $third_DB->select('*');
  $third_DB->from('DEPARTMENTS');
  $third_DB->where('DEPTID', $id);
  $query = $third_DB->get();

  return $query->row();
}

function get_karyawan_by_dept()
{
  $ci = &get_instance();
  $third_DB   = $ci->load->database('attendance', TRUE);
  $dept_id    = $ci->session->userdata('user_dept_id');

  $third_DB->select('*');
  $third_DB->from('USERINFO');
  $third_DB->where('DEFAULTDEPTID', $dept_id);
  $third_DB->order_by('USERID', 'ASC');
  $query = $third_DB->get();

  return $query->result();
}

function get_karyawan_($nip)
{
  $ci = &get_instance();
  $third_DB   = $ci->load->database('attendance', TRUE);

  $third_DB->select('*');
  $third_DB->from('USERINFO');
  $third_DB->where('SSN', $nip);
  $third_DB->order_by('USERID', 'ASC');
  $query  = $third_DB->get();
  $data   = $query->row();

  return $data->NAME;
}

function get_karyawan_details($id)
{
  $ci = &get_instance();
  $third_DB   = $ci->load->database('attendance', TRUE);

  $third_DB->select('A.*, B.DEPTNAME');
  $third_DB->from('USERINFO A');
  $third_DB->join('DEPARTMENTS B', 'B.DEPTID = A.DEFAULTDEPTID', 'LEFT');
  $third_DB->where('SSN', $id);
  $query  = $third_DB->get();
  $cek    = $query->num_rows();
  if ($cek > 0) {
    return $query->row();
  } else {
    return "-";
  }
}
