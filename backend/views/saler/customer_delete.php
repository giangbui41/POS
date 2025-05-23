<?php
    include('backend/core/function.php');

    $paramResultId = checkParamId('id');
    if(is_numeric($paramResultId)){
        $customerId = validate($paramResultId);

        $customer = getById('khachhang',$customerId);

        if($customer['status']==200){
            // Xóa tất cả hóa đơn liên quan (nếu cần)
            $makhToDelete = $customer['data']['MAKH'];
            $deleteHoaDonQuery = "DELETE FROM hoadon WHERE MAKH = '$makhToDelete'";
            $deleteHoaDonResult = mysqli_query($con, $deleteHoaDonQuery);

            if ($deleteHoaDonResult || mysqli_affected_rows($con) == 0) { // Xóa thành công hoặc không có hóa đơn liên quan
                $response = delete('khachhang', $customerId);
                if($response){
                    redirect('/nhomhuongnoi/source/saler/customer','Xóa khách hàng thành công !');
                } else {
                    redirect('/nhomhuongnoi/source/saler/customer','Lỗi khi xóa khách hàng.');
                }
            } else {
                redirect('/nhomhuongnoi/source/saler/customer','Lỗi khi xóa hóa đơn liên quan.');
            }

        } else {
            redirect('/nhomhuongnoi/source/saler/customer',$customer['message']);
        }
    } else {
        redirect('/nhomhuongnoi/source/saler/customer','ID khách hàng không hợp lệ.');
    }
?>