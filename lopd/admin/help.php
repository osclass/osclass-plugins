<?php if (!defined('OC_ADMIN') || OC_ADMIN!==true) exit('Access is not allowed.');
    /*
     *      OSCLass – software for creating and publishing online classified
     *                           advertising platforms
     *
     *                        Copyright (C) 2010 OSCLASS
     *
     *       This program is free software: you can redistribute it and/or
     *     modify it under the terms of the GNU Affero General Public License
     *     as published by the Free Software Foundation, either version 3 of
     *            the License, or (at your option) any later version.
     *
     *     This program is distributed in the hope that it will be useful, but
     *         WITHOUT ANY WARRANTY; without even the implied warranty of
     *        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     *             GNU Affero General Public License for more details.
     *
     *      You should have received a copy of the GNU Affero General Public
     * License along with this program.  If not, see <http://www.gnu.org/licenses/>.
     */
?>
<div id="settings_form" style="border: 1px solid #ccc; background: #eee; ">
    <div style="padding: 20px;">
        <div style="float: left; width: 100%;">
            <fieldset>
                <legend><?php _e('Ayuda LOPD', 'lopd'); ?></legend>
                <h3><?php _e('Pasos a seguir para adaptarse a la LOPD', 'lopd'); ?></h3>
                <ol>
                    <li><?php _e('Inscripción de fichos en AEPD', 'lopd'); ?></li>
                    <li><?php _e('Redacción del documento de seguridad', 'lopd'); ?></li>
                    <li><?php _e('La calidad de los datos', 'lopd'); ?></li>
                    <li><?php _e('El derecho de información en la recogida de los datos (art.5)', 'lopd'); ?></li>
                    <li><?php _e('Consentimiento del afectado (art.6)', 'lopd'); ?></li>
                    <li><?php _e('El deber de secreto', 'lopd'); ?></li>
                    <li><?php _e('El ejercicio de los derechos de ARCO', 'lopd'); ?></li>
                </ol>
                <br/>
                <br/>
                <br/>
                <h3><?php _e('1.-Inscripción de los ficheros en el Registro General de la Protección de Datos. Artículo 26 LOPD. Artículos. 5 y 6 R.D 1332/1994, de 20 de Junio. ', 'lopd'); ?></h3>
                <p><?php _e('Para esto hay que ir a:', 'lopd'); ?></p>

                <p><a href="http://agpd.es/portalweb/canalresponsable/inscripcion_ficheros/Obtencion_formulario/index-ides-idphp.php" >http://agpd.es/portalweb/canalresponsable/inscripcion_ficheros/Obtencion_formulario/index-ides-idphp.php</a></p>

                <p><?php echo sprintf(__('Elegir el Formulario NOTA de titularidad privada (<a href="%s">Aqui</a>)', 'lopd'), 'http://agpd.es/portalweb/canalresponsable/inscripcion_ficheros/Notificaciones_tele/obtencion_formulario/common/pdfs/Titularidad_Privada.pdf'); ?></p>

                <p><?php echo sprintf(__('Guía para rellenar el formulario (<a href="%s">Aqui</a>) donde nos explican como cumplimentarlo y las 3 maneras que para enviarlor:', 'lopd'), 'http://agpd.es/portalweb/canalresponsable/inscripcion_ficheros/Notificaciones_tele/obtencion_formulario/common/pdfs/Guia_rapida_Sistema_NOTA.pdf'); ?></p>
                <p><?php _e('Internet + certificado digital: se hace todo por Internet y es inmediato.', 'lopd'); ?></p>
                <p><?php _e('Internet sin certificado digital: se rellena por Internet y el sistema devuelve una solicitud que hay que enviar a la AEPD', 'lopd'); ?></p>
                <p><?php _e('Presentación en papel: se rellena la solicitud, se imprime y se hace llegar a AEPD.', 'lopd'); ?></p>

                <p><?php _e('La dirección donde hay que enviar las solicitudes:', 'lopd'); ?></p>

                <p>Agencia Española de protección de datos<br/>
                Jorge Juan 6<br/>
                28001- Madrid<br/>
                </p>

                <br/>
                <br/>
                <br/>

                <h3><?php _e('2.-Redacción del documento de seguridad.', 'lopd'); ?></h3>

                <p><?php _e('"El responsable del fichero elaborará e implantará la normativa de seguridad mediante un documento de seguridad de obligado cumplimiento para el personal con acceso a los datos automatizados de carácter personal y a los sistemas de información" R.D 994/1999, de 11 de Junio.', 'lopd'); ?></p> 
                <p><?php _e('Redacción de cláusulas de protección de datos. Artículo 5 LOPD. ', 'lopd'); ?></p>

                <p><?php _e('Una vez recibido la confirmación de la incripción redactaremos el Documento de Seguridad, del cual tenemos una guia en:', 'lopd'); ?></p>

                <p><a href="http://agpd.es/portalweb/canalresponsable/guia_documento/index-ides-idphp.php" >http://agpd.es/portalweb/canalresponsable/guia_documento/index-ides-idphp.php</a></p>

                <p><b><?php _e('Para completar este apartado, tendremos que partir de la guía base y adaptarla a nuestro caso particular.', 'lopd'); ?></b></p>
                <br/>
                <br/>
                <br/>

                <h3><?php _e('3.-La calidad de los datos', 'lopd'); ?></h3>

                <p><?php _e('La Ley tiende a establecer unos principios generales de Calidad tendentes a garantizar un uso adecuado de los datos, fijando que:', 'lopd'); ?></p>
                <ol>
                <li><?php _e('Los datos personales deben de adecuarse a la finalidad para la que fueron recabados,', 'lopd'); ?></li>
                <li><?php _e('Deben ser exactos y actualizados, ', 'lopd'); ?></li>
                <li><?php _e('No deben mantenerse indefinidamente sin justificación, y ', 'lopd'); ?></li>
                <li><?php _e('Deben haber sido recogidos de forma lícita.', 'lopd'); ?></li>
                </ol>

                <p><?php echo sprintf(__('<a href="%s">Fuente</a>. Ver la fuente para profundizar y contemplar las infracciones derivadas de este punto.', 'lopd'), 'http://microsoft.com/spain/empresas/guia_lopd/calidad_datos.mspx'); ?></p>

                <p><?php _e('Para cumplir con este apartado habría que diseñar un procedimiento de recogida de datos que se adecue a lo que necesitamos para nuestro proposito, o sea, un formulario donde se muestren todos los datos que vamos a recoger e informar al usuario mediante las condiciones de uso que sus datos van a ser utilizados para “xxx” fin y van a ser mantenidos hasta que el utilice sus derechos de ARCO o su cuenta sea borrada del sistema mediante un formulario una vez logueado para evitar la baja de su cuenta por terceros.', 'lopd'); ?></p>
                <p><b><?php _e('Este plugin ya cumple con este apartado, informando a la hora del registro de que deben aceptar la política de privacidad (modificable desde el panel de administrador)', 'lopd'); ?></b></p>

                <br/>
                <br/>
                <br/>

                <h3><?php _e('4.-El derecho de información en la recogida de los datos (art.5)', 'lopd'); ?></h3>
                <p><?php _e('La LOPD establece una obligación previa de informar al afectado a la hora de recabar sus datos personales de una serie de extremos, para que el afectado pueda suministrar o no sus datos con el pleno conocimiento del alcance del tratamiento que se va a realizar. ', 'lopd'); ?></p>


                <p><?php _e('Se debe informar al interesado de forma previa, expresa, precisa e inequívoca de:', 'lopd'); ?></p>
                <ul>
                <li><?php _e('De la existencia de un fichero o tratamiento de datos de carácter personal, de la finalidad de la recogida de los datos y de los destinatarios de la información.', 'lopd'); ?></li>
                <li><?php _e('Del carácter obligatorio o facultativo de su respuesta a las preguntas que les sean planteadas.', 'lopd'); ?></li>
                <li><?php _e('De las consecuencias de la obtención de los datos o de la negativa a suministrarlos.', 'lopd'); ?></li>
                <li><?php _e('De la posibilidad de ejercitar los derechos de acceso, rectificación, cancelación y oposición.', 'lopd'); ?></li>
                <li><?php _e('De la identidad y dirección del responsable del tratamiento, o en su caso, de su representante.', 'lopd'); ?></li>
                </ul>

                <p><?php _e('Como indicamos anteriormente, si los datos son recabados mediante cuestionarios, formularios u otros impresos (en formato papel o electrónico), las menciones anteriores deberán figurar en los mismos de forma claramente legible. ', 'lopd'); ?></p>

                <p><?php _e('Para cumplir con este apartado habría que introducir en el formulario un texto similar :', 'lopd'); ?></p>

                <p><?php _e('“De conformidad con la Ley Orgánica 15/1999 de Protección de Datos Personales y a través de la cumplimentación del presente formulario, Vd. presta su consentimiento para el tratamiento de sus datos personales facilitados, que serán incorporados al fichero “XXXXXXX”, titularidad de la EMPRESA XXX, inscrito en el Registro General de la Agencia Española de Protección de Datos, cuya finalidad es la gestión fiscal, contable y administrativa de la relación contractual, así como el envío de información comercial sobre nuestros productos y servicios. ', 'lopd'); ?></p>
                <p><?php _e('Igualmente le informamos que podrá ejercer los derechos de acceso, rectificación, cancelación y oposición establecidos en dicha Ley a través de carta certificada, adjuntando fotocopia de su DNI/Pasaporte, en la siguiente dirección: EMPRESA XXX. Departamento de Atención al Cliente LOPD. C/XXXXXX nº X. 46000 Valencia.', 'lopd'); ?></p>

                <p><?php echo sprintf(__('<a href="">Fuente</a>', 'lopd'), 'http://microsoft.com/spain/empresas/guia_lopd/derecho_informacion.mspx'); ?></p>

                <p><?php _e('Aún qué también se podría incluir un enlace a las politicas de uso (con el texto “utilizando el servicio acepta las politicas de uso”) en las cuales se establecería una cláusula con el texto anterior. De esta forma el formulario queda mucho más limpio y cumpliría con la LOPD.', 'lopd'); ?></p>
                <p><b><?php _e('Este plugin ya cumple con este apartado, incluyendo dicho enlace en el formulario de registro para evitar colocar todo el texto.', 'lopd'); ?></b></p>

                <br/>
                <br/>
                <br/>

                <h3><?php _e('5.-Consentimiento del afectado (art.6)', 'lopd'); ?></h3>
                <p><?php _e('El principio del consentimiento es el eje fundamental de la Protección de Datos estableciéndose como exigencia. Así lo indica al establecer, en el art.6.1 que –“El tratamiento de los datos de carácter personal requerirá el consentimiento inequívoco del afectado, salvo que la ley disponga otra cosa”-. ', 'lopd'); ?></p>

                <p><?php _e('El consentimiento no es más que la manifestación de voluntad, libre, inequívoca, específica e informada, mediante la que el interesado consiente el tratamiento de sus datos personales. ', 'lopd'); ?></p>

                <p><?php _e('La ley fija como tipo general el consentimiento libre, específico, informado e inequívoco, salvo que la propia ley disponga tipos especiales. Así, podemos decir que el consentimiento será:', 'lopd'); ?></p>
                <ul>
                <li><?php _e('a) Libre: deberá haber sido obtenido sin la intervención de vicio alguno del consentimiento.', 'lopd'); ?></li>
                <li><?php _e('b) Específico: referido a una determinada operación de tratamiento y para una finalidad determinada, explícita y legítima del Responsable del Fichero. ', 'lopd'); ?></li>
                <li><?php _e('c) Informado: el usuario debe conocer, con anterioridad al tratamiento, la existencia y las finalidades para las que se recogen los datos. ', 'lopd'); ?></li>
                <li><?php _e('d) Inequívoco: es preciso que exista expresamente una acción u omisión que implique la existencia del consentimiento (no resulta admisible el consentimiento presunto).', 'lopd'); ?></li>
                </ul>
                <p><?php _e('En principio, el consentimiento dado abarca todas y cada una de las operaciones que engloban el tratamiento; encontrando sólo una excepción, que es en el caso de la cesión de datos, donde el consentimiento para la cesión será previo e informado.', 'lopd'); ?></p>

                <p><?php _e('Hay que señalar que el consentimiento general exigido por la ley va íntimamente ligado a la obligación de informar, a la hora de la recogida de los datos, de los extremos señalados en el art.5 (derecho de información en la recogida), puesto que entiende la Ley que a partir de dicha información el afectado es consciente y toma conocimiento de la existencia del tratamiento que se va a realizar, las finalidades y los derechos que le asisten. ', 'lopd'); ?></p>

                <p><?php echo sprintf(__('<a href="%s">Fuente</a>', 'lopd'), 'http://microsoft.com/spain/empresas/guia_lopd/consentimiento_afectado.mspx'); ?></p>


                <p><?php _e('Para cumplir con este apartado habría que incluir las clausulas necesarias en las politicas de uso, haciendo referencia a ellas en el formulario de recogida de datos.', 'lopd'); ?></p>
                <p><b><?php _e('Este plugin ya cumple con este apartado, incluyendo dicho enlace en el formulario de registro para evitar colocar todo el texto y siendo necesario aceptar dichas condiciones para proceder con el registro.', 'lopd'); ?></b></p>
                <br/>
                <br/>
                <br/>

                <h3><?php _e('6.- El deber de secreto', 'lopd'); ?></h3>

                <p><?php _e('Se establece un deber de secreto y de custodia al que se ven sujetos las personas que participan en el proceso de tratamiento de los datos de carácter personal; deberes que subsistirán una vez finalizado el tratamiento de los datos. Así, se establece que –“El responsable del fichero y quienes intervengan en cualquier fase del tratamiento de los datos de carácter personal están obligados al secreto profesional respecto de los mismos y al deber de guardarlos, obligaciones que subsistirán aun después de finalizar sus respectivas relaciones con el titular del fichero o, en su caso, con el responsable del mismo”-.', 'lopd'); ?></p>

                <p><?php _e('Este deber de secreto debe ser adoptado por todo el personal laboral que accede a los Ficheros de datos; además en las empresas, este deber fijado por la Ley se ve complementado por la obligación profesional de confidencialidad y secreto descrita en el art.5.a) del Estatuto de los Trabajadores, que se mantiene vigente hasta la finalización de la relación laboral.', 'lopd'); ?></p>

                <p><?php _e('Por ello, las obligaciones de Secreto, Confidencialidad y Custodia incumben a todo el personal; y, de manera particular, a aquellos que en el desarrollo de sus funciones accedan a Ficheros que contienen datos personales.', 'lopd'); ?></p>
                fuente

                <p><b><?php _e('Para cumplir con este apartado habrá que crear contratos, cláusulas y procedimientos que le permitan dar a conocer tanto a empleados como colaboradores su deber de secreto y las consecuencias de su incumplimiento. En caso de ser un webmaster solitario, no haría falta crear nada y bastaría con no contarle nada a nadie.', 'lopd'); ?></b></p>

                <br/>
                <br/>
                <br/>

                <h3><?php _e('7.- El ejercicio de los derechos de ARCO', 'lopd'); ?></h3>
                <p><?php _e('El empresario debe facilitar a los ciudadanos el ejercicio de los denominados derechos Arco (acceso, rectificación, cancelación y oposición). ', 'lopd'); ?></p>

                <p><b><?php _e('Este plugin ayuda con este apartado, ofreciendo los derechos antes descritos.', 'lopd'); ?></b></p>
            </fieldset>
        </div>
        <div style="clear: both;"></div>										
    </div>
</div>
