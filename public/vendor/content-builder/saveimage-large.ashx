<%@ WebHandler Language="VB" Class="saveimage" %>

Imports System
Imports System.Web
Imports System.IO
Imports System.Drawing
Imports System.Drawing.Imaging
Imports System.Drawing.Drawing2D

Public Class saveimage : Implements IHttpHandler

    'Specify url path
    Private sPath As String = "uploads/"
    
    Public Sub ProcessRequest(ByVal context As HttpContext) Implements IHttpHandler.ProcessRequest
        context.Response.ContentType = "text/html"
        
        'Prepare uploads folder
        Dim sMapPath As String = context.Server.MapPath(sPath)
        If Not sMapPath.EndsWith("\") Then sMapPath = sMapPath & "\"
        If Not My.Computer.FileSystem.DirectoryExists(sMapPath) Then
            My.Computer.FileSystem.CreateDirectory(sMapPath)
        End If

        Dim customvalue As String = context.Request.Form("hidRefId") 'Custom value (optional). If you set "customval" parameter in ContentBuilder, the value can be read here.
        
        Dim filecover As HttpPostedFile = context.Request.Files("fileImage")
        
        Dim filename As String
        Dim fileext As String
        Try
            filename = Path.GetFileName(filecover.FileName)
            fileext = Path.GetExtension(filecover.FileName)
        Catch ex As Exception
            context.Response.Write("<html><body onload=""alert('No file!')""></body></html>")
            Exit Sub
        End Try
        
        
        'Check if uses is authorized here
        Dim bAuthorized As Boolean = True 'Set authorized as example result
        If Not bAuthorized Then
            context.Response.Write("<html><body onload=""alert('Not Authorized')""></body></html>")
            Exit Sub
        End If
        

        'Generate random file name here
        Dim myRnd As New Random()
        Dim chars() As Char = New Char() {"A"c, "B"c, "C"c, "D"c, "E"c, "F"c, _
                                          "G"c, "H"c, "I"c, "J"c, "K"c, "L"c, _
                                          "M"c, "N"c, "O"c, "P"c, "Q"c, "R"c, _
                                          "S"c, "T"c, "U"c, "V"c, "W"c, "X"c, _
                                          "Y"c, "Z"c, "0"c, "1"c, "2"c, "3"c, _
                                          "4"c, "5"c, "6"c, "7"c, "8"c, "9"c, _
                                          "a"c, "b"c, "c"c, "d"c, "e"c, "f"c, _
                                          "g"c, "h"c, "i"c, "j"c, "k"c, "l"c, _
                                          "m"c, "n"c, "o"c, "p"c, "q"c, "r"c, _
                                          "s"c, "t"c, "u"c, "v"c, "w"c, "x"c, _
                                          "y"c, "z"c}
        Dim n As Integer = 5
        Dim rndStr As String = String.Empty
        Dim i As Integer = 0
        While i < n
            rndStr += chars(myRnd.Next(0, 62))
            System.Math.Max(System.Threading.Interlocked.Increment(i), i - 1)
        End While
        rndStr = rndStr & Now.Hour & Now.Minute & Now.Second
        
        
        Dim pagefolder As String = sMapPath
        Dim pagefolderpath As String = sPath
        
        'Optional: If using customvalue to specify upload folder
        If customvalue <> "" Then
            pagefolder = pagefolder & customvalue & "\"
            pagefolderpath = pagefolderpath & customvalue & "/"
            
            If Not My.Computer.FileSystem.DirectoryExists(sMapPath & customvalue) Then
                My.Computer.FileSystem.CreateDirectory(sMapPath & customvalue)
            End If
        End If
        
        
        'Save image
        'filecover.SaveAs(pagefolder & rndStr & fileext)
        CreateCover(filecover, 1600, 1600, rndStr & ".jpg", pagefolder) 'Resize image to max 1600x1600 to safe size
        
        'Replace image src with the new saved file
        context.Response.Write("<html><body onload=""parent.applyLargerImage('" & pagefolderpath & rndStr & ".jpg" & "')""></body></html>")
    End Sub
 
    Public ReadOnly Property IsReusable() As Boolean Implements IHttpHandler.IsReusable
        Get
            Return False
        End Get
    End Property
    
    Protected Sub CreateCover(ByVal PostedFile As HttpPostedFile, ByVal nThumbWidth As Integer, ByVal nThumbHeigth As Integer, ByVal sThumbFileName As String, ByVal sFolder As String)

        If Not Directory.Exists(sFolder) Then
            Directory.CreateDirectory(sFolder)
        End If

        With PostedFile
            If (.FileName.Substring(.FileName.LastIndexOf(".") + 1).ToLower = "jpg" Or .FileName.Substring(.FileName.LastIndexOf(".") + 1).ToLower = "gif" _
                Or .FileName.Substring(.FileName.LastIndexOf(".") + 1).ToLower = "png" Or .FileName.Substring(.FileName.LastIndexOf(".") + 1).ToLower = "jpeg") Then

                Dim nJpegQuality As Integer = 90

                Dim imgOri As System.Drawing.Image
                imgOri = System.Drawing.Image.FromStream(.InputStream)

                Dim nNewWidth As Integer = imgOri.Size.Width
                Dim nNewHeight As Integer = imgOri.Size.Height
                If nNewHeight < nThumbHeigth Then
                    'noop
                Else
                    'width priority
                    nNewHeight = nNewHeight * (nThumbWidth / nNewWidth)
                    nNewWidth = nThumbWidth
                End If
                'If nNewWidth < nThumbWidth And nNewHeight < nThumbHeigth Then
                '    'noop
                'ElseIf nNewWidth / nNewHeight > nThumbWidth / nThumbHeigth Then
                '    nNewHeight = nNewHeight * (nThumbWidth / nNewWidth)
                '    nNewWidth = nThumbWidth
                'ElseIf nNewWidth / nNewHeight < nThumbWidth / nThumbHeigth Then
                '    nNewWidth = nNewWidth * (nThumbHeigth / nNewHeight)
                '    nNewHeight = nThumbHeigth
                'Else
                '    nNewWidth = nThumbWidth
                '    nNewHeight = nThumbHeigth
                'End If

                Dim imgThumb As System.Drawing.Image = New System.Drawing.Bitmap(nNewWidth, nNewHeight)
                Dim gr As System.Drawing.Graphics = System.Drawing.Graphics.FromImage(imgThumb)
                gr.InterpolationMode = System.Drawing.Drawing2D.InterpolationMode.HighQualityBicubic
                gr.SmoothingMode = System.Drawing.Drawing2D.SmoothingMode.HighQuality
                gr.PixelOffsetMode = System.Drawing.Drawing2D.PixelOffsetMode.HighQuality
                gr.CompositingQuality = System.Drawing.Drawing2D.CompositingQuality.HighQuality
                gr.DrawImage(imgOri, 0, 0, nNewWidth, nNewHeight)

                Dim info() As System.Drawing.Imaging.ImageCodecInfo = System.Drawing.Imaging.ImageCodecInfo.GetImageEncoders()
                Dim ePars As System.Drawing.Imaging.EncoderParameters = New System.Drawing.Imaging.EncoderParameters(1)
                ePars.Param(0) = New System.Drawing.Imaging.EncoderParameter(System.Drawing.Imaging.Encoder.Quality, nJpegQuality)
                'imgThumb.Save(sFolder & sThumbFileName, info(1), ePars)
                imgThumb.Save(sFolder & sThumbFileName, System.Drawing.Imaging.ImageFormat.Jpeg)

                imgThumb.Dispose()
                imgOri.Dispose()

            End If
        End With
    End Sub

End Class