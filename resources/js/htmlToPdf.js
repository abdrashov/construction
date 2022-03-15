import html2Canvas from 'html2canvas'
import JsPDF from 'jspdf'
export default {
    getPdf: (idStr, title) => {
        html2Canvas(document.querySelector('#' + idStr), {
            // allowTaint: true,
            useCORS: true,
        }).then(function (canvas) {
            let contentWidth = canvas.width
            let contentHeight = canvas.height
            // Одна страница pdf отображает высоту холста, созданного html-страницей;
            let pageHeight = (contentWidth / 592.28) * 841.89
            // Высота html-страницы, которая генерирует pdf
            let leftHeight = contentHeight
            // Смещение страницы
            let position = 0
            // Размер бумаги формата A4 [595,28,841,89], ширина и высота холста, создаваемого страницей html в pdf
            let imgWidth = 595.28
            let imgHeight = (592.28 / contentWidth) * contentHeight
            // canvas.crossOrigin="anonymous";
            let pageData = canvas.toDataURL('image/jpeg', 1.0)

            let PDF = new JsPDF('', 'pt', 'a4')
            // Следует различать две высоты: одна - это фактическая высота html-страницы и высота страницы, которая генерирует pdf (841.89)
            // Когда содержимое не превышает диапазон, отображаемый на одной странице pdf, страницы нет необходимости
            if (leftHeight < pageHeight) {
                PDF.addImage(pageData, 'JPEG', 0, 0, imgWidth, imgHeight)
            } else {
                while (leftHeight > 0) {
                    PDF.addImage(pageData, 'JPEG', 0, position, imgWidth, imgHeight)
                    leftHeight -= pageHeight
                    position -= 841.89
                    if (leftHeight > 0) {
                        // Избегайте добавления пустых страниц
                        PDF.addPage()
                    }
                }
            }
            PDF.save(title + '.pdf')
        })
    },
}
