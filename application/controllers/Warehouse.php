<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Warehouse extends CI_Controller
{

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */

	public function __construct()
	{
		parent::__construct();

		$this->load->helper(array('url', 'form', 'cookie'));
		$this->load->library(array('session', 'cart'));

		$this->load->model('auth_model', 'auth');
		if ($this->auth->isNotLogin());

		//START ADD THIS FOR USER ROLE MANAGMENT
		$this->contoller_name = $this->router->class;
		$this->function_name 	= $this->router->method;
		$this->load->model('Rolespermissions_model');
		//END

		$this->load->model('Dashboard_model');
		$this->load->model('perusahaan_model', 'perusahaan');
		$this->load->model('barcode_model', 'barcode_sales');
	}

	public function index()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {

			$data['group_halaman'] 	= "Warehouse";
			$data['nama_halaman'] 	= "Scan Barcode";
			$data['icon_halaman'] 	= "icon-airplay";
			$data['perusahaan'] 		= $this->perusahaan->get_details();

			//ADDING TO LOG
			$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";

			log_helper($log_url, $log_type, $log_data);
			//END LOG

			$this->load->view('adminx/warehouse/cari_barcode_sales', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
	}

	public function cari_barcode()
	{
		$barcode				= $this->input->post('code_barcode');
		$barcode_array	= explode('|', $barcode);
		$part_id 				= $barcode_array[0];
		$nomor_do 			= $barcode_array[1];
		$qty_order 			= $barcode_array[2];

		$data_barcode 	= $this->barcode_sales->get_data_print_do_new($barcode, $part_id, $nomor_do, $qty_order);
		if (count($data_barcode) > 0) {
			echo json_encode(
				array(
					"status_code" => 200,
					"status" 			=> "success",
					"message" 		=> "Sukses menampilkan barcode " . $barcode,
					"data" 				=> $data_barcode
				)
			);
		} else {
			echo json_encode(
				array(
					"status_code" => 404,
					"status" 			=> "error",
					"message" 		=> "Barcode " . $barcode . " tidak ditemukan!",
					"data" 				=> array()
				)
			);
		}
	}

	public function cari_barcode_OLD()
	{
		$id 						= $this->input->post('code_barcode');
		//$data_barcode 	= $this->barcode_sales->get_by_id($id);
		$data_barcode 	= $this->barcode_sales->get_part_terbaru($id);
		$text 					= "";

		if (count($data_barcode) > 0) {

			$no = 1;
			foreach ($data_barcode as $key => $value) {
				$no_do = $value->nodo;
				$cust  = $this->get_customer_name($no_do);
				$val 	 = "'" . $value->nodo . "', '" . $value->pocustomer . "', '" . $value->barcodeid . "', '" . $cust->PartnerName . "'";
				$text .= 	'<tr>
										<td><button id="btn_approved_' . $no++ . '" class="btn btn-danger" onclick="open_modal_driver(' . $val . ')">APPROVED</button></td>
										<td>' . $value->nodo . '</td>
										<td>' . $value->pocustomer . '</td>
										<td>' . $cust->PartnerName . '</td>
										<td>' . $value->barcodeid . '</td>
										<td>' . $value->partid . '</td>
										<td>' . $value->partname . '</td>
										<td><span class="badge bg-info" style="font-size: 18px;">' . $value->seqstiker . '</span></td>
										<td>' . $value->qtyorder . '</td>
										<td>' . $value->qtypallet . '</td>
									</tr>';
			};

			echo json_encode(
				array(
					"status_code" => 200,
					"status" 			=> "success",
					"message" 		=> "Sukses menampilkan barcode " . $id,
					"html" 				=> $text,
					"data" 				=> $data_barcode,
					"customer"		=> $cust
				)
			);
		} else {
			echo json_encode(
				array(
					"status_code" => 404,
					"status" 			=> "error",
					"message" 		=> "Barcode " . $id . " tidak ditemukan!",
					"html" 				=> array(),
					"data" 				=> array()
				)
			);
		}
	}

	private function get_customer_name($no_do)
	{
		$second_DB  = $this->load->database('bjsmas01_db', TRUE);
		$do_array 	= explode('/', $no_do);
		$thn_bln_do = $do_array[2];
		$table_name	= "trans_SJHD" . $thn_bln_do;
		$query 			= $second_DB->query("SELECT a.*, b.PartnerName FROM $table_name a
																		 LEFT JOIN Ms_Partner b ON b.PartnerID = a.ReceiverID
																		 WHERE NoBukti = '$no_do'
																		 ORDER BY a.TGL DESC");

		return $query->row();
	}

	public function approved_status()
	{
		//CEK APAKAH BARCODE SUDAH ADA DI TABLE APPROVAL
		$no_do 					= $this->input->post('no_do');
		$no_po 					= $this->input->post('no_po');
		$nm_customer 		= strtoupper($this->input->post('nm_customer'));
		$no_barcode 		= $this->input->post('no_barcode');
		$array 					= explode('|', $no_barcode);
		$part_id 				= $this->input->post('part_no');
		$qty_order 			= $this->input->post('qty_order');
		$nama_driver 		= ucwords($this->input->post('nama_driver'));
		$no_polisi 			= strtoupper($this->input->post('no_polisi'));

		//cek apakah sudah ada barcode, po dan do yg sama
		$second_DB = $this->load->database('bjsmas01_db', TRUE);

		$where = array(
			'barcode_id' 	=> $no_barcode,
			'no_po' 			=> $no_po,
			'no_do' 			=> $no_do,
			'part_id' 		=> $part_id,
			'qty_order' 	=> $qty_order,
		);
		$second_DB->select('*');
		$second_DB->from('tbl_scanbarcode_approval');
		$second_DB->where($where);
		$cek_po_do = $second_DB->get();
		$hasil_cek = $cek_po_do->num_rows();

		//cek pertama
		if ($hasil_cek == 0) {

			//cek apakah sudah ada dengan PO, DO, Part ID dan Qty yang sama
			$q1 = $second_DB->query("SELECT * FROM tbl_scanbarcode_approval 
															 WHERE no_po = '$no_po' 
															 AND no_do = '$no_do' 
															 AND part_id = '$part_id'
															 AND qty_order = '$qty_order'");
			$hasil_cek_2 = $q1->num_rows();
			//cek kedua
			if ($hasil_cek_2 == 0) {
				$data = array(
					'barcode_id' 		=> $no_barcode,
					'no_po' 				=> $no_po,
					'no_do' 				=> $no_do,
					'part_id' 			=> $part_id,
					'qty_order' 		=> $qty_order,
					'nama_customer' => $nm_customer,
					'nama_driver' 	=> $nama_driver,
					'no_polisi' 		=> $no_polisi,
					'lokasi_id' 		=> "WH001",
					'lokasi_scan' 	=> "DELIVERY",
					'approved_by' 	=> $this->session->userdata('user_code'),
					'create_date'		=> date('Y-m-d H:i:s')
				);
				$insert = $this->barcode_sales->save($data);
				if ($insert) {
					echo json_encode(
						array(
							"status_code" => 200,
							"status" 			=> "success",
							"message" 		=> "Barcode " . $no_barcode . " sukses disimpan",
							"data"				=> $insert
						)
					);
				} else {
					echo json_encode(
						array(
							"status_code" => 500,
							"status" 			=> "error",
							"message" 		=> "Barcode " . $no_barcode . " gagal disimpan",
							"data" 				=> array()
						)
					);
				}
			} else {
				echo json_encode(
					array(
						"status_code" => 500,
						"status" 			=> "error",
						"message" 		=> "Barcode " . $no_barcode . " dengan DO, PO dan Part ID diatas sudah terdaftar",
						"data" 				=> array()
					)
				);
			}
		} else {
			echo json_encode(
				array(
					"status_code" => 500,
					"status" 			=> "error",
					"message" 		=> "Barcode " . $no_barcode . " sudah di scan",
					"data" 				=> array()
				)
			);
		}
	}

	public function approved_status_OLD()
	{
		//CEK APAKAH BARCODE SUDAH ADA DI TABLE APPROVAL
		$id 						= $this->input->post('id');
		$no_do 					= $this->input->post('no_do');
		$no_po 					= $this->input->post('no_po');
		$nm_customer 		= strtoupper($this->input->post('nm_customer'));
		$no_barcode 		= $this->input->post('no_barcode');
		$part_no_array 	= explode('-', $this->input->post('part_no'));
		$part_no 				= $part_no_array[1];
		$nama_driver 		= ucwords($this->input->post('nama_driver'));
		$no_polisi 			= strtoupper($this->input->post('no_polisi'));

		//cek apakah sudah ada barcode, po dan do yg sama
		$second_DB = $this->load->database('bjsmas01_db', TRUE);

		$where = array(
			'barcode_id' 	=> $no_barcode,
			'no_po' 			=> $no_po,
			'no_do' 			=> $no_do
		);
		$second_DB->select('*');
		$second_DB->from('tbl_scanbarcode_approval');
		$second_DB->where($where);
		$cek_po_do = $second_DB->get();
		$hasil_cek = $cek_po_do->num_rows();

		//cek pertama
		if ($hasil_cek == 0) {

			//cek apakah sudah ada dengan PO dan DO yang sama
			$q1 = $second_DB->query("SELECT * FROM tbl_scanbarcode_approval 
															 WHERE no_po = '$no_po' AND no_do = '$no_do' 
															 AND barcode_id LIKE '%$part_no%'");
			$hasil_cek_2 = $q1->num_rows();
			//cek kedua
			if ($hasil_cek_2 == 0) {
				$data = array(
					'barcode_id' 		=> $no_barcode,
					'no_po' 				=> $no_po,
					'no_do' 				=> $no_do,
					'nama_customer' => $nm_customer,
					'nama_driver' 	=> $nama_driver,
					'no_polisi' 		=> $no_polisi,
					'lokasi_id' 		=> "WH001",
					'lokasi_scan' 	=> "DELIVERY",
					'approved_by' 	=> $this->session->userdata('user_code'),
					'create_date'		=> date('Y-m-d H:i:s')
				);
				$insert = $this->barcode_sales->save($data);
				if ($insert) {
					echo json_encode(
						array(
							"status_code" => 200,
							"status" 			=> "success",
							"message" 		=> "Barcode " . $no_barcode . " sukses disimpan",
							"data"				=> $insert
						)
					);
				} else {
					echo json_encode(
						array(
							"status_code" => 500,
							"status" 			=> "error",
							"message" 		=> "Barcode " . $no_barcode . " gagal disimpan",
							"data" 				=> array()
						)
					);
				}
			} else {
				echo json_encode(
					array(
						"status_code" => 500,
						"status" 			=> "error",
						"message" 		=> "Barcode " . $no_barcode . " dengan PO dan DO diatas sudah terdaftar",
						"data" 				=> array()
					)
				);
			}
		} else {
			echo json_encode(
				array(
					"status_code" => 500,
					"status" 			=> "error",
					"message" 		=> "Barcode " . $no_barcode . " sudah terdaftar",
					"data" 				=> array()
				)
			);
		}
	}

	public function produk_terkirim()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {

			$data['group_halaman'] 	= "Warehouse";
			$data['nama_halaman'] 	= "Barang Terkirim";
			$data['icon_halaman'] 	= "icon-package";
			$data['perusahaan'] 		= $this->perusahaan->get_details();

			//ADDING TO LOG
			$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";

			log_helper($log_url, $log_type, $log_data);
			//END LOG

			$this->load->view('adminx/warehouse/produk_terkirim', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
	}

	public function produk_terkirim_list()
	{
		$draw 			= intval($this->input->get("draw"));
		$start 			= intval($this->input->get("start"));
		$length 		= intval($this->input->get("length"));

		$tanggal 		= $this->input->post('tanggal');
		$bulan 			= $this->input->post('bulan');
		$tahun 			= $this->input->post('tahun');

		$second_DB  = $this->load->database('bjsmas01_db', TRUE);

		if ($tanggal != 'All' && $bulan != 'All') {
			$where = " DAY(A.create_date) = '" . $tanggal . "'
								AND MONTH(A.create_date) = '" . $bulan . "'
								AND YEAR(A.create_date) = '" . $tahun . "'";
		}

		if ($tanggal == 'All' && $bulan != 'All') {
			$where = " MONTH(A.create_date) = '" . $bulan . "'
								AND YEAR(A.create_date) = '" . $tahun . "'";
		}

		if ($tanggal == 'All' && $bulan == 'All') {
			$where = " YEAR(A.create_date) = '" . $tahun . "'";
		}

		$sql 		= "SELECT A.id, A.lokasi_scan, A.create_date, A.approved_by, A.no_po, A.no_do, A.part_id, 
								A.qty_order, A.nama_customer, A.nama_driver, A.no_polisi, A.create_date, A.barcode_id, 
								A.part_id,
								B.seqstiker, B.qtypallet,B.createtime,
								C.Namadivisi, 
								D.PartName 
							FROM 
								tbl_scanbarcode_approval A 
								LEFT JOIN Ms_Part D ON D.PartID = A.part_id 
								LEFT JOIN tbl_msdivisi C ON A.lokasi_id = C.Iddivisi 
								LEFT JOIN (
									(
										SELECT 
											* 
										FROM 
											tbl_printqrcodedo
									) 
									UNION ALL 
										(
											SELECT 
												* 
											FROM 
												tbl_printqrcodedoulang
										)
								) B ON B.barcodeid = A.barcode_id 
							WHERE $where
							ORDER BY 
								A.create_date DESC";
		$query 	= $second_DB->query($sql);
		$data 	= [];
		$no 		= 1;

		foreach ($query->result() as $value) {
			$data_box 		= explode('/', $value->seqstiker);
			$data[] = array(
				$no++,
				'<a class="text-danger" href="scan_details/' . base64_encode($value->no_do) . '/' . base64_encode($value->no_po) . '/' . $value->part_id . '/' . $value->qty_order . '/' . date('Y-m-d', strtotime($value->createtime)) . '" title="Klik more" target="_blank">' . $value->no_do . '</a>',
				$value->no_po,
				$value->barcode_id,
				'<span class="badge badge-success" style="font-size: 14px;">' . $value->lokasi_scan . '</span>',
				get_created_by($value->approved_by),
				$value->Namadivisi,
				substr($value->create_date, 0, -4),
				'<span class="badge badge-danger" style="font-size: 17px;">' . $data_box[1] . '</span>',
				number_format($value->qtypallet),
				number_format($value->qty_order),
				$value->part_id,
				$value->PartName,
				$value->nama_customer,
				$value->nama_driver . " (" . $value->no_polisi . ")"
			);
		}

		$result = array(
			"draw" 						=> $draw,
			"recordsTotal" 		=> $query->num_rows(),
			"recordsFiltered" => $query->num_rows(),
			"data" 						=> $data
		);

		echo json_encode($result);
		exit();
	}

	public function produk_terkirim_list_range()
	{
		$draw 			= intval($this->input->get("draw"));
		$start 			= intval($this->input->get("start"));
		$length 		= intval($this->input->get("length"));

		$start_date = $this->input->post('start_date');
		$end_date 	= $this->input->post('end_date');

		$second_DB  = $this->load->database('bjsmas01_db', TRUE);

		$sql 		= "SELECT A.id, A.lokasi_scan, A.create_date, A.approved_by, A.no_po, A.no_do, A.part_id, 
								A.qty_order, A.nama_customer, A.nama_driver, A.no_polisi, A.create_date, A.barcode_id, 
								A.part_id,
								B.seqstiker, B.qtypallet,B.createtime,
								C.Namadivisi, 
								D.PartName 
							FROM 
								tbl_scanbarcode_approval A 
								LEFT JOIN Ms_Part D ON D.PartID = A.part_id 
								LEFT JOIN tbl_msdivisi C ON A.lokasi_id = C.Iddivisi 
								LEFT JOIN (
									(
										SELECT 
											* 
										FROM 
											tbl_printqrcodedo
									) 
									UNION ALL 
										(
											SELECT 
												* 
											FROM 
												tbl_printqrcodedoulang
										)
								) B ON B.barcodeid = A.barcode_id 
							WHERE CAST(A.create_date as date) between '$start_date' and '$end_date'
							ORDER BY 
								A.create_date DESC";

		$query 	= $second_DB->query($sql);
		$data 	= [];
		$no 		= 1;

		foreach ($query->result() as $value) {

			$data_box 			= explode('/', $value->seqstiker);

			$data[] = array(
				$no++,
				'<a class="text-danger" href="scan_details/' . base64_encode($value->no_do) . '/' . base64_encode($value->no_po) . '/' . $value->part_id . '/' . $value->qty_order . '/' . date('Y-m-d', strtotime($value->createtime)) . '" title="Klik more" target="_blank">' . $value->no_do . '</a>',
				$value->no_po,
				$value->barcode_id,
				'<span class="badge badge-success" style="font-size: 14px;">' . $value->lokasi_scan . '</span>',
				get_created_by($value->approved_by),
				$value->Namadivisi,
				substr($value->create_date, 0, -4),
				'<span class="badge badge-danger" style="font-size: 17px;">' . $data_box[1] . '</span>',
				number_format($value->qtypallet),
				number_format($value->qty_order),
				$value->part_id,
				$value->PartName,
				$value->nama_customer,
				$value->nama_driver . " (" . $value->no_polisi . ")"
			);
		}

		$result = array(
			"draw" 						=> $draw,
			"recordsTotal" 		=> $query->num_rows(),
			"recordsFiltered" => $query->num_rows(),
			"data" 						=> $data
		);

		echo json_encode($result);
		exit();
	}

	public function scan_details()
	{
		$data['group_halaman'] 	= "Warehouse";
		$data['nama_halaman'] 	= "Scan Details";
		$data['icon_halaman'] 	= "icon-package";
		$data['perusahaan'] 		= $this->perusahaan->get_details();
		$data['no_do'] 					= base64_decode($this->uri->segment(3));
		$data['no_po'] 					= base64_decode($this->uri->segment(4));
		$data['part_id'] 				= $this->uri->segment(5);
		$data['qty_order'] 			= $this->uri->segment(6);
		$data['po_date'] 				= $this->uri->segment(7);

		//ADDING TO LOG
		$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
		$log_type 	= "VIEW";
		$log_data 	= "";

		log_helper($log_url, $log_type, $log_data);
		//END LOG

		$this->load->view('adminx/warehouse/scan_details', $data, FALSE);
	}

	public function scan_details_list()
	{
		$draw 			= intval($this->input->get("draw"));
		$start 			= intval($this->input->get("start"));
		$length 		= intval($this->input->get("length"));

		$no_do 			= $this->input->post('no_do');
		$no_po 			= $this->input->post('no_po');
		$part_id 		= $this->input->post('part_id');
		$qty_order 	= $this->input->post('qty_order');
		$po_date 		= $this->input->post('po_date');

		$second_DB  = $this->load->database('bjsmas01_db', TRUE);

		$sql = "SELECT * FROM tbl_printqrcodedo 
						WHERE CAST(createtime AS date) = '$po_date' 
						AND nodo = '$no_do' 
						AND pocustomer = '$no_po'
						AND partid = '$part_id'
						ORDER BY barcodeid";
		$query	= $second_DB->query($sql);
		$data 	= [];

		foreach ($query->result() as $value) {

			$data[] = array(
				$value->seqstiker,
				$value->barcodeid,
				$value->partid,
				$value->partname,
				number_format($value->qtyorder),
				number_format($value->qtypallet),
				$value->nodo,
				$value->pocustomer,
				$value->keterangan == '' ? '-' : $value->keterangan,
				$value->customer,
				$value->createtime
			);
		}

		$result = array(
			"draw" 						=> $draw,
			"recordsTotal" 		=> $query->num_rows(),
			"recordsFiltered" => $query->num_rows(),
			"data" 						=> $data
		);

		echo json_encode($result);
		exit();
	}

	public function produk_terkirim_list_OLD()
	{
		$draw 			= intval($this->input->get("draw"));
		$start 			= intval($this->input->get("start"));
		$length 		= intval($this->input->get("length"));

		$tanggal 		= $this->input->post('tanggal');
		$bulan 			= $this->input->post('bulan');
		$tahun 			= $this->input->post('tahun');

		$second_DB  = $this->load->database('bjsmas01_db', TRUE);

		if ($tanggal != 'All' && $bulan != 'All') {
			$where = "DAY(A.create_date) = '" . $tanggal . "'
								AND MONTH(A.create_date) = '" . $bulan . "'
								AND YEAR(A.create_date) = '" . $tahun . "'";
			$second_DB->where($where);
		}

		if ($tanggal == 'All' && $bulan != 'All') {
			$where = "MONTH(A.create_date) = '" . $bulan . "'
								AND YEAR(A.create_date) = '" . $tahun . "'";
			$second_DB->where($where);
		}

		if ($tanggal == 'All' && $bulan == 'All') {
			$where = "YEAR(A.create_date) = '" . $tahun . "'";
			$second_DB->where($where);
		}

		$second_DB->select('A.id, A.lokasi_scan, A.create_date, A.approved_by, 
    										A.nama_customer, A.nama_driver, A.no_polisi, A.create_date, 
												A.barcode_id, C.Namadivisi'); // B.*
		$second_DB->from('tbl_scanbarcode_approval A');
		//$second_DB->join('tbl_scanbarcode B', 'A.barcode_id = B.barcodeid', 'left');
		$second_DB->join('tbl_msdivisi C', 'A.lokasi_id = C.Iddivisi', 'left');
		$second_DB->order_by('A.create_date', 'DESC');
		$query = $second_DB->get();

		$data 	= [];
		$no 		= 1;

		foreach ($query->result() as $value) {

			$id = $value->barcode_id;
			$data_barcode 	= $this->barcode_sales->get_newes_part($id);
			//print_r($data_barcode[0]->partno);
			//exit;

			$data_box = explode('/', $data_barcode[0]->qtybox);

			$data[] = array(
				$no++,
				$value->barcode_id,
				'<span class="badge badge-success" style="font-size: 14px;">' . $value->lokasi_scan . '</span>',
				get_created_by($value->approved_by),
				$value->Namadivisi,
				substr($value->create_date, 0, -4),
				//'<span class="badge badge-danger" style="font-size: 17px;">' . $data_box[1] . '</span>',
				'<span class="badge badge-danger" style="font-size: 17px;">' . $data_box[1] . '</span>',
				number_format($data_barcode[0]->qtyorder), //number_format($value->qtyorder),
				$data_barcode[0]->partno, //$value->partno,
				$data_barcode[0]->partname, //$value->partname,
				$data_barcode[0]->nodoc, //$value->nodoc,
				$data_barcode[0]->po, //$value->po,
				$value->nama_customer,
				$value->nama_driver . " (" . $value->no_polisi . ")"
			);
		}

		$result = array(
			"draw" 						=> $draw,
			"recordsTotal" 		=> $query->num_rows(),
			"recordsFiltered" => $query->num_rows(),
			"data" 						=> $data
		);

		echo json_encode($result);
		exit();
	}

	public function summary_barang_delivery()
	{
		//CHECK FOR ACCESS FOR EACH FUNCTION
		$user_level 			= $this->session->userdata('user_level');
		$check_permission =  $this->Rolespermissions_model->check_permissions($this->contoller_name, $this->function_name, $user_level);
		if ($check_permission->num_rows() == 1) {
			$data['group_halaman'] 	= "Warehouse";
			$data['nama_halaman'] 	= "Ringkasan Barang Delivery";
			$data['icon_halaman'] 	= "icon-package";
			$data['perusahaan'] 		= $this->perusahaan->get_details();

			//ADDING TO LOG
			$log_url 		= base_url() . $this->contoller_name . "/" . $this->function_name;
			$log_type 	= "VIEW";
			$log_data 	= "";

			log_helper($log_url, $log_type, $log_data);
			//END LOG

			$this->load->view('adminx/warehouse/barang_delivery', $data, FALSE);
		} else {
			redirect('errorpage/error403');
		}
	}

	public function summary_barang_delivery_list()
	{
		$draw 			= intval($this->input->get("draw"));
		$start 			= intval($this->input->get("start"));
		$length 		= intval($this->input->get("length"));

		$tanggal 		= $this->input->post('tanggal');
		$bulan 			= $this->input->post('bulan');
		$tahun 			= $this->input->post('tahun');

		$second_DB  = $this->load->database('bjsmas01_db', TRUE);

		if ($tanggal != 'All' && $bulan != 'All') {
			$WHERE = "WHERE DAY(a.createtime) = " . $tanggal . " AND MONTH(a.createtime) = " . $bulan . " AND YEAR(a.createtime) = " . $tahun;
		}

		if ($tanggal == 'All' && $bulan != 'All') {
			$WHERE = "WHERE MONTH(a.createtime) = " . $bulan . " AND YEAR(a.createtime) = " . $tahun;
		}

		if ($tanggal == 'All' && $bulan == 'All') {
			$WHERE = "WHERE YEAR(a.createtime) = " . $tahun;
		}

		$sql = "SELECT CASE WHEN SUM(BRC) <> QTYBOX THEN 'PROCESS' ELSE 'COMPLETED' end STATUS,
						SUM(BRC) QRCODE, a.PARTID, a.PARTNAME, SUM(a.QTYORDER) QTYORDER, a.QTYPALLET, 
						QTYBOX, a.POCUSTOMER, a.NODO, CAST(a.createtime AS date) AS tgl_scan, a.customer
						FROM
						(SELECT COUNT(a.barcodeid) BRC, a.PARTID, a.PARTNAME, a.QTYORDER, a.QTYPALLET, 
						SUBSTRING(a.seqstiker,3,1)AS QTYBOX,  a.POCUSTOMER, a.NODO, a.createtime, a.customer
						FROM tbl_printqrcodedo a
						LEFT JOIN tbl_scanbarcode b ON a.barcodeid = b.barcodeid
						GROUP BY a.PARTID, a.PARTNAME, a.QTYORDER, a.QTYPALLET, a.seqstiker, 
						a.POCUSTOMER, a.NODO, a.createtime, a.customer ) a
						$WHERE
						GROUP BY a.BRC, a.PARTID, a.PARTNAME, a.QTYPALLET, QTYBOX, a.POCUSTOMER, 
						a.NODO, CAST(a.createtime AS date), a.customer
						ORDER BY CAST(a.createtime AS date) DESC";

		$query 			= $second_DB->query($sql);
		$result 		= $query->result();
		$data 			= [];
		$no 				= 1;
		$status 		= "";
		$lokasi_1 	= "";
		$lokasi_2 	= "";

		foreach ($result as $key => $value) {

			$data[] = array(
				$no++,
				$value->NODO,
				$value->POCUSTOMER,
				$value->PARTID,
				$value->PARTNAME,
				number_format($value->QTYORDER, 0),
				$value->customer
			);
		}


		$result = array(
			"draw" 						=> $draw,
			"recordsTotal" 		=> $query->num_rows(),
			"recordsFiltered" => $query->num_rows(),
			"data" 						=> $data
		);

		echo json_encode($result);
		exit();
	}

	public function cek_lokasi_scan($nodoc, $po)
	{
		$second_DB  = $this->load->database('bjsmas01_db', TRUE);
		$query 			= $second_DB->query("SELECT TOP 1 A.barcodeid, A.nodoc, A.po, 
																		A.createtime AS tgl_masuk_lokasi_1, 
																		B.Namadivisi AS lokasi_1, C.lokasi_id, C.lokasi_scan AS lokasi_2, 
																		C.create_date AS tgl_pengiriman
																		FROM tbl_scanbarcode A
																		LEFT JOIN tbl_msdivisi B ON B.Iddivisi = A.lokasiscan
																		LEFT JOIN tbl_scanbarcode_approval C ON C.barcode_id = A.barcodeid
																		WHERE A.nodoc = '$nodoc' AND A.po = '$po' AND C.lokasi_scan IS NOT NULL");
		$cek 				= $query->num_rows();

		return $query->result();
	}
}
