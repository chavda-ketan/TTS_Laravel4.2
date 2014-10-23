
$bydate=get_adwords_rms($row['d_f'],$d_t,$rowa['rms'],$rowa['id']);

@foreach($dates as $day => $info)


                    ?>
                    <tr class="<?php if ($k>1) { echo 'hideass'; } ?> a_<?php if ($k>1) { echo $id; } ?> <?php switch($k) { case 1: echo'xoxo'; break; case 2: echo'active'; break; case 3: echo'warning'; break; case 4: echo'eheh'; break; case 5: echo'xoxoa'; break; case 6: echo'xoxob'; break; } ?>" >
                        <td><a href="javascript:void(0)" title="Campaigns: <?php echo $bydate['c']; ?>"><b><?php echo $row['d_f']; ?> - <?php echo $d_t; ?></b></a><p><b><i class="fa fa-mobile"></i> <?php // echo $bydate[c]; ?></b> </p></td>
                        <td><b><?php echo $bydate['clicks']; ?></b> <p><b><?php echo $bydate['clicks_m']; ?></b></p></td>
                        <td><b><?php echo $bydate['ctr']; ?>%</b> <p><b><?php echo $bydate['ctr_m']; ?>%</b></p></td>
                        <td><b><?php echo $bydate['qs']; ?></b> <p><b><?php echo $bydate['qs_m']; ?></b></p></td>
                        <td><b><?php echo $bydate['pos']; ?></b> <p><b><?php echo $bydate['pos_m']; ?></b></p></td>
                        <td><b>$<?php echo $bydate['cpc']; ?></b> <p><b>$<?php echo $bydate['cpc_m']; ?></b></p></td>
                        <td><b><?php echo $bydate['rms_wo']; ?></b></td>
                        <td><b><?php echo number_format(($bydate['clicks']+$bydate['clicks_m'])/$bydate['rms_wo'],1); ?></b></td>
                        <td><b>$<?php echo number_format(($bydate['cost']+$bydate['cost_m'])/$bydate['rms_wo']); ?></b></td>
                        <td><b>$<?php echo number_format(($bydate['cost']+$bydate['cost_m'])/($bydate['rms_sale'])*100); ?></b></td>
                        <td><b>$<?php echo $bydate['cost']; ?></b> <p><b>$<?php echo $bydate['cost_m']; ?></b></p></td>
                        <td><b>$<?php echo $bydate['rms_sale']; ?></b></td>
                        <td><b>$<?php echo number_format($bydate['rms_sale']/$bydate['rms_wo']); ?></b></td>
                        <td><b><?php echo $bydate['imp']; ?></b> <p><b><?php echo $bydate['imp_m']; ?></b></p></td>
                        <td><a class='gaybutts' data-id='<?php echo $row['id']; ?>' href=''><i class="fa fa-times"></i></a></td>
                    </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
@endforeach