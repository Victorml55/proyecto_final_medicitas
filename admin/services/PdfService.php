<?php

// ============================================================
// Servicio de PDF – TCPDF
// Sprint 06: Librería de generación de documentos PDF
// ============================================================

require_once __DIR__ . '/../../vendor/autoload.php';

class PdfService {

    public static function comprobanteCita(array $cita): void {

        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

        $pdf->SetCreator('MediCitas');
        $pdf->SetAuthor('MediCitas Sistema');
        $pdf->SetTitle('Comprobante de Cita #' . $cita['id_cita']);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(false, 0);
        $pdf->AddPage();

        // Paleta
        $azul      = [0,   95,  153];
        $azulOsc   = [0,   60,  110];
        $azulClaro = [230, 243, 252];
        $grisClaro = [247, 249, 251];
        $grisMed   = [220, 228, 236];
        $grisText  = [100, 116, 139];
        $negro     = [30,  41,   59];
        $blanco    = [255, 255, 255];
        $verde     = [16,  185, 129];

        $W      = 210;
        $margen = 18;
        $cardW  = $W - ($margen * 2);

        // ── HEADER ─────────────────────────────────────────────
        $pdf->SetFillColor(...$azul);
        $pdf->Rect(0, 0, $W, 52, 'F');

        $pdf->SetFillColor(...$azulOsc);
        $pdf->Rect(0, 42, $W, 10, 'F');

        $pdf->SetFont('helvetica', 'B', 24);
        $pdf->SetTextColor(...$blanco);
        $pdf->SetXY(18, 10);
        $pdf->Cell(0, 10, 'MediCitas', 0, 1, 'L');

        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetTextColor(180, 215, 240);
        $pdf->SetXY(18, 21);
        $pdf->Cell(0, 6, 'Sistema de Gestión de Citas Médicas', 0, 1, 'L');

        $folio = str_pad($cita['id_cita'], 6, '0', STR_PAD_LEFT);
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->SetTextColor(...$blanco);
        $pdf->SetXY(0, 12);
        $pdf->Cell($W - 18, 6, 'FOLIO #' . $folio, 0, 1, 'R');

        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetTextColor(180, 215, 240);
        $pdf->SetXY(0, 20);
        $pdf->Cell($W - 18, 5, date('d/m/Y H:i') . ' hrs', 0, 1, 'R');

        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetTextColor(...$blanco);
        $pdf->SetXY(18, 44);
        $pdf->Cell(0, 6, 'COMPROBANTE DE CITA MEDICA', 0, 0, 'L');

        $estado = strtoupper($cita['nombre_estado'] ?? 'PENDIENTE');
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetFillColor(...$verde);
        $pdf->SetTextColor(...$blanco);
        $pdf->SetXY($W - 58, 43);
        $pdf->Cell(40, 7, $estado, 0, 0, 'C', true);

        // ── FONDO GRIS ─────────────────────────────────────────
        $pdf->SetFillColor(...$grisClaro);
        $pdf->Rect(0, 52, $W, 245, 'F');

        // ── HELPERS ────────────────────────────────────────────
        $drawCard = function($titulo, $yPos, $altura) use ($pdf, $margen, $cardW, $blanco, $azul, $grisMed) {
            $pdf->SetFillColor(200, 210, 220);
            $pdf->RoundedRect($margen + 0.8, $yPos + 0.8, $cardW, $altura, 3, '1111', 'F');
            $pdf->SetFillColor(...$blanco);
            $pdf->RoundedRect($margen, $yPos, $cardW, $altura, 3, '1111', 'F');
            $pdf->SetFillColor(...$azul);
            $pdf->Rect($margen, $yPos, 3, $altura, 'F');
            $pdf->SetFont('helvetica', 'B', 8);
            $pdf->SetTextColor(...$azul);
            $pdf->SetXY($margen + 8, $yPos + 4);
            $pdf->Cell(0, 5, mb_strtoupper($titulo), 0, 1, 'L');
            $pdf->SetDrawColor(...$grisMed);
            $pdf->SetLineWidth(0.2);
            $pdf->Line($margen + 4, $yPos + 12, $margen + $cardW - 4, $yPos + 12);
        };

        $drawFila = function($etiqueta, $valor, $yPos, $col = 0) use ($pdf, $margen, $cardW, $grisText, $negro) {
            $xBase = $margen + 8 + ($col * ($cardW / 2));
            $pdf->SetFont('helvetica', '', 7.5);
            $pdf->SetTextColor(...$grisText);
            $pdf->SetXY($xBase, $yPos);
            $pdf->Cell(35, 5, $etiqueta . ':', 0, 0, 'L');
            $pdf->SetFont('helvetica', 'B', 8);
            $pdf->SetTextColor(...$negro);
            $pdf->SetXY($xBase + 35, $yPos);
            $pdf->Cell(50, 5, $valor, 0, 0, 'L');
        };

        $y = 62;

        // ── CARD 1: DATOS DE LA CITA ───────────────────────────
        $drawCard('Informacion de la cita', $y, 58);

        $fecha     = date('d/m/Y', strtotime($cita['fecha_cita']));
        $dias      = ['Sunday'=>'Domingo','Monday'=>'Lunes','Tuesday'=>'Martes',
                      'Wednesday'=>'Miercoles','Thursday'=>'Jueves',
                      'Friday'=>'Viernes','Saturday'=>'Sabado'];
        $dia  = $dias[date('l', strtotime($cita['fecha_cita']))] ?? '';
        $hIni = substr($cita['hora_inicio'] ?? '', 0, 5);
        $hFin = substr($cita['hora_fin']    ?? '', 0, 5);

        $drawFila('Fecha',        "$dia $fecha",               $y + 16, 0);
        $drawFila('Horario',      "$hIni - $hFin hrs",         $y + 16, 1);
        $drawFila('Consultorio',  $cita['numero_consultorio'] ?? 'No asignado', $y + 27, 0);
        if (!empty($cita['codigo_confirmacion'])) {
            $drawFila('Codigo',   $cita['codigo_confirmacion'], $y + 27, 1);
        }
        if ($cita['costo'] !== null) {
            $drawFila('Costo',    '$' . number_format((float)$cita['costo'], 2) . ' MXN', $y + 38, 0);
        }

        $y += 66;

        // ── CARD 2: PACIENTE + MÉDICO ──────────────────────────
        $halfW = ($cardW / 2) - 3;

        // Paciente izq
        $pdf->SetFillColor(200, 210, 220);
        $pdf->RoundedRect($margen + 0.8, $y + 0.8, $halfW, 52, 3, '1111', 'F');
        $pdf->SetFillColor(...$blanco);
        $pdf->RoundedRect($margen, $y, $halfW, 52, 3, '1111', 'F');
        $pdf->SetFillColor(...$azul);
        $pdf->Rect($margen, $y, 3, 52, 'F');
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetTextColor(...$azul);
        $pdf->SetXY($margen + 8, $y + 4);
        $pdf->Cell(0, 5, 'PACIENTE', 0, 1);
        $pdf->SetDrawColor(...$grisMed);
        $pdf->SetLineWidth(0.2);
        $pdf->Line($margen + 4, $y + 12, $margen + $halfW - 4, $y + 12);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetTextColor(...$negro);
        $pdf->SetXY($margen + 8, $y + 16);
        $pdf->Cell($halfW - 12, 6, $cita['nombre_paciente'] ?? '', 0, 1);
        if (!empty($cita['telefono_paciente'])) {
            $pdf->SetFont('helvetica', '', 7.5);
            $pdf->SetTextColor(...$grisText);
            $pdf->SetXY($margen + 8, $y + 25);
            $pdf->Cell(18, 5, 'Tel:', 0, 0);
            $pdf->SetTextColor(...$negro);
            $pdf->Cell(0, 5, $cita['telefono_paciente'], 0, 1);
        }
        if (!empty($cita['email_paciente'])) {
            $pdf->SetFont('helvetica', '', 7);
            $pdf->SetTextColor(...$grisText);
            $pdf->SetXY($margen + 8, $y + 33);
            $pdf->Cell(18, 5, 'Email:', 0, 0);
            $pdf->SetTextColor(...$negro);
            $pdf->Cell(0, 5, $cita['email_paciente'], 0, 1);
        }

        // Médico der
        $xMed = $margen + $halfW + 6;
        $pdf->SetFillColor(200, 210, 220);
        $pdf->RoundedRect($xMed + 0.8, $y + 0.8, $halfW, 52, 3, '1111', 'F');
        $pdf->SetFillColor(...$blanco);
        $pdf->RoundedRect($xMed, $y, $halfW, 52, 3, '1111', 'F');
        $pdf->SetFillColor(...$azul);
        $pdf->Rect($xMed, $y, 3, 52, 'F');
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetTextColor(...$azul);
        $pdf->SetXY($xMed + 8, $y + 4);
        $pdf->Cell(0, 5, 'MEDICO ASIGNADO', 0, 1);
        $pdf->SetDrawColor(...$grisMed);
        $pdf->Line($xMed + 4, $y + 12, $xMed + $halfW - 4, $y + 12);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetTextColor(...$negro);
        $pdf->SetXY($xMed + 8, $y + 16);
        $pdf->Cell($halfW - 12, 6, 'Dr(a). ' . ($cita['nombre_medico'] ?? ''), 0, 1);
        $pdf->SetFont('helvetica', '', 7.5);
        $pdf->SetTextColor(...$grisText);
        $pdf->SetXY($xMed + 8, $y + 25);
        $pdf->Cell(0, 5, 'Especialidad:', 0, 1);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetTextColor(...$negro);
        $pdf->SetXY($xMed + 8, $y + 32);
        $pdf->Cell(0, 5, $cita['nombre_especialidad'] ?? '', 0, 1);

        $y += 60;

        // ── CARD 3: MOTIVO ─────────────────────────────────────
        if (!empty($cita['motivo_consulta'])) {
            $drawCard('Motivo de consulta', $y, 36);
            $pdf->SetFont('helvetica', '', 8.5);
            $pdf->SetTextColor(...$negro);
            $pdf->SetXY($margen + 8, $y + 16);
            $pdf->MultiCell($cardW - 12, 5.5, $cita['motivo_consulta'], 0, 'L');
        }

        // ── FOOTER ─────────────────────────────────────────────
        $pdf->SetFillColor(...$azul);
        $pdf->Rect(0, 277, $W, 20, 'F');
        $pdf->SetFont('helvetica', 'I', 7.5);
        $pdf->SetTextColor(180, 215, 240);
        $pdf->SetXY(0, 282);
        $pdf->Cell($W, 5,
            'Documento generado automaticamente por MediCitas  |  ' . date('d/m/Y H:i') . '  |  Folio #' . $folio,
            0, 0, 'C');

        $pdf->Output('cita_' . $folio . '.pdf', 'D');
    }
}
